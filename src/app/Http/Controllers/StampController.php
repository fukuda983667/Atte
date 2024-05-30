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

        $attendanceRecord = AttendanceRecord::where('user_id', $clockOut['user_id'])
                                        ->whereNotNull('clock_in')
                                        ->whereNull('clock_out')
                                        ->first();

        if ($attendanceRecord) {
            $attendanceRecord->update(['clock_out' => $clockOut['clock_out']]);
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

        $breakRecord = BreakRecord::where('user_id', $endTime['user_id'])
                            ->whereNotNull('start_time')
                            ->whereNull('end_time')
                            ->first();

        if ($breakRecord) {
            $breakRecord->update(['end_time' => $endTime['end_time']]);
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
