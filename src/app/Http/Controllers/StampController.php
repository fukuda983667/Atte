<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StampController extends Controller
{
    // 打刻ページ(ホーム)の表示
    public function index()
    {
        // 勤務中かどうかを確認する existsはtrueかfalseで値を返す。
        $isWorking = AttendanceRecord::where('user_id', auth()->id())
                                    ->whereNotNull('clock_in')
                                    ->whereNull('clock_out')
                                    ->exists();

        // 休憩中かどうかを確認する
        $isOnBreak = BreakRecord::where('user_id', auth()->id())
                                ->whereNotNull('start_time')
                                ->whereNull('end_time')
                                ->exists();

        return view('index', compact('isWorking', 'isOnBreak'));
    }

    // 出勤時間の保存
    public function storeClockIn(Request $request)
    {
        $clockIn = $request->only(['user_id', 'clock_in']);
        AttendanceRecord::create($clockIn);

        return redirect()->route('index');
    }


    // 退勤時間の保存
    public function storeClockOut(Request $request)
    {
        $clockOut = $request->only(['user_id', 'clock_out']);
        $clockOutTime = Carbon::parse($clockOut['clock_out']);

        $attendanceRecord = AttendanceRecord::where('user_id', $clockOut['user_id'])
                                        ->whereNotNull('clock_in')
                                        ->whereNull('clock_out')
                                        ->first();

        if ($attendanceRecord) {
            $clockInTime = Carbon::parse($attendanceRecord->clock_in);

            if ($clockInTime->isSameDay($clockOutTime)) {
                // 同じ日の場合
                $attendanceRecord->update(['clock_out' => $clockOut['clock_out']]);
            } else {
                // 日付をまたぐ場合。copy()はCarbonインスタンスを使い回すため。
                // endOfDay()メソッドでその日の23:59:59を返す。
                $endOfDay = $clockInTime->copy()->endOfDay();
                // endOfDayに一秒足して翌日00:00:00を格納
                $startOfNextDay = $endOfDay->copy()->addSecond();

                // 前日のレコードを23:59:59で更新
                $attendanceRecord->update(['clock_out' => $endOfDay]);

                // 日付をまたぐ場合の処理
                do {
                    $nextEndOfDay = $startOfNextDay->copy()->endOfDay();

                    if ($clockOutTime->isSameDay($startOfNextDay)) {
                        // 日付跨ぎが一回の場合
                        AttendanceRecord::create([
                            'user_id' => $clockOut['user_id'],
                            'clock_in' => $startOfNextDay,
                            'clock_out' => $clockOutTime->format('Y-m-d H:i:s'),
                        ]);
                    } else {
                        // 多くの日をまたぐ場合
                        AttendanceRecord::create([
                            'user_id' => $clockOut['user_id'],
                            'clock_in' => $startOfNextDay,
                            'clock_out' => $nextEndOfDay,
                        ]);
                    }
                    $startOfNextDay = $nextEndOfDay->copy()->addSecond();
                } while ($clockOutTime->greaterThan($startOfNextDay));
            }
        }

        return redirect()->route('index');
    }


    // 休憩開始時間の保存
    public function storeStartTime(Request $request)
    {
        $startTime = $request->only(['user_id','start_time']);

        $attendanceRecord = AttendanceRecord::where('user_id', $startTime['user_id'])
                                        ->whereNotNull('clock_in')
                                        ->whereNull('clock_out')
                                        ->first();

        if ($attendanceRecord) {
            $attendanceRecordId = $attendanceRecord->id;
            $startTime['attendance_record_id'] = $attendanceRecordId;
        }

        BreakRecord::create($startTime);

        return redirect()->route('index');
    }


    // 休憩終了時間の保存
    public function storeEndTime(Request $request)
    {
        $endTime = $request->only(['user_id','end_time']);
        $endTimeObj = Carbon::parse($endTime['end_time']);//打刻時の日時だけを格納

        $breakRecord = BreakRecord::where('user_id', $endTime['user_id'])
                            ->whereNotNull('start_time')
                            ->whereNull('end_time')
                            ->first();

        if ($breakRecord) {
            $startTime = Carbon::parse($breakRecord->start_time);

            if ($startTime->isSameDay($endTimeObj)) {
                // 同じ日の場合
                $breakRecord->update(['end_time' => $endTime['end_time']]);
            } else {
                // 日付をまたぐ場合
                $endOfDay = $startTime->copy()->endOfDay(); // 23:59:59
                $startOfNextDay = $endOfDay->copy()->addSecond();

                // 前日のレコードを23:59:59で更新
                $breakRecord->update(['end_time' => $endOfDay]);

                // 前日の勤務終了処理
                $attendanceRecord = AttendanceRecord::where('user_id', $endTime['user_id'])
                                                    ->whereNotNull('clock_in')
                                                    ->whereNull('clock_out')
                                                    ->first();
                if ($attendanceRecord) {
                    $attendanceRecord->update(['clock_out' => $endOfDay]);
                }

                // 日付をまたぐ場合の処理
                do {
                    $nextEndOfDay = $startOfNextDay->copy()->endOfDay();

                    if ($endTimeObj->isSameDay($startOfNextDay)) {
                        // 休憩終了打刻と00:00:00休憩開始の日付が同じになったら
                        AttendanceRecord::create([
                            'user_id' => $endTime['user_id'],
                            'clock_in' => $startOfNextDay,
                        ]);
                        BreakRecord::create([
                            'user_id' => $endTime['user_id'],
                            'attendance_record_id' => AttendanceRecord::latest()->first()->id, // 直近のレコードからid取得
                            'start_time' => $startOfNextDay,
                            'end_time' => $endTime['end_time'],
                        ]);
                    } else {
                        // 多くの日をまたぐ場合の処理
                        AttendanceRecord::create([
                            'user_id' => $endTime['user_id'],
                            'clock_in' => $startOfNextDay,
                            'clock_out' => $nextEndOfDay,
                        ]);
                        BreakRecord::create([
                            'user_id' => $endTime['user_id'],
                            'attendance_record_id' => AttendanceRecord::latest()->first()->id,
                            'start_time' => $startOfNextDay,
                            'end_time' => $nextEndOfDay,
                        ]);
                    }
                    $startOfNextDay = $nextEndOfDay->copy()->addSecond();
                } while ($endTimeObj->greaterThan($startOfNextDay));
            }
        }
        return redirect()->route('index');
    }


    // 日付別勤怠ページの表示
    public function list(Request $request)
    {
        // リクエストから日付を取得、デフォルトは今日
        $date = $request->input('date', Carbon::today()->toDateString());

        // 日付をCarbonインスタンスに変換
        $date = Carbon::parse($date);

        // 指定された日付の勤怠記録を取得、clock_in時間でソート
        $attendanceRecords = AttendanceRecord::whereDate('clock_in', $date)
            ->whereNotNull('clock_in')
            ->whereNotNull('clock_out')
            ->orderBy('clock_in', 'asc')
            ->paginate(5); // 1ページに5件ずつ表示

        // 各勤怠記録に対して休憩時間と勤務時間を計算
        foreach ($attendanceRecords as $record) {
            $totalBreakTime = $record->breakRecords->sum(function($break) {
                return strtotime($break->end_time) - strtotime($break->start_time);
            });

            $workTime = strtotime($record->clock_out) - strtotime($record->clock_in) - $totalBreakTime;

            // 勤怠記録に計算結果を追加
            $record->totalBreakTime = gmdate('H:i:s', $totalBreakTime);
            $record->workTime = gmdate('H:i:s', $workTime);

            // 勤務開始時間と勤務終了時間を H:i:s 形式に変換
            $record->formatted_clock_in = date('H:i:s', strtotime($record->clock_in));
            $record->formatted_clock_out = date('H:i:s', strtotime($record->clock_out));
        }

        return view('list', compact('attendanceRecords', 'date'));
    }
}
