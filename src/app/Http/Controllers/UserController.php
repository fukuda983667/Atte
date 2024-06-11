<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserController extends Controller
{
    // ユーザ一覧を表示
    public function userList()
    {
        $users = User::paginate(5);

        foreach ($users as $user) {
            // 勤務中かどうかを確認する
            $isWorking = AttendanceRecord::where('user_id', $user->id)
                                        ->whereNotNull('clock_in')
                                        ->whereNull('clock_out')
                                        ->exists();

            // 休憩中かどうかを確認する
            $isOnBreak = BreakRecord::where('user_id', $user->id)
                                    ->whereNotNull('start_time')
                                    ->whereNull('end_time')
                                    ->exists();

            // ステータスの判定
            if ($isOnBreak) {
                $user->status = '休憩中';
            } elseif ($isWorking) {
                $user->status = '勤務中';
            } else {
                $user->status = '退勤';
            }
        }

        return view('userList', compact('users'));
    }

    public function userAttendance(Request $request)
    {
        // リクエストからユーザーIDを取得
        $userId = $request->input('id');
        $user = User::find($userId);

        // 指定されたユーザの勤怠記録を取得
        $attendanceRecords = AttendanceRecord::where('user_id', $userId)
            ->whereNotNull('clock_in')
            ->whereNotNull('clock_out')
            ->orderBy('clock_in', 'asc')
            ->paginate(5); // 1ページに5件ずつ表示

        // 各勤怠記録に対して休憩時間と勤務時間を計算
        foreach ($attendanceRecords as $record) {
            $breakRecords = $record->breakRecords->filter(function($break) {
                return !is_null($break->start_time) && !is_null($break->end_time);
            });

            $totalBreakTime = $breakRecords->sum(function($break) {
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

        return view('userAttendance', compact('attendanceRecords', 'user'));
    }
}
