<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use App\Models\User;
use Carbon\Carbon;

class HandleAttendanceAtMidnight extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:handle-midnight';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle attendance and breaks at midnight';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::all();
        $now = Carbon::now()->startOfDay();
        $midnight = $now->copy()->subSecond(); // 23:59:59
        $nextDay = $now->copy(); // 00:00:00

        foreach ($users as $user) {
            // 勤務中のレコードを取得
            $attendanceRecord = AttendanceRecord::where('user_id', $user->id)
                ->whereNotNull('clock_in')
                ->whereNull('clock_out')
                ->first();

            // 勤務中だった場合の処理
            if ($attendanceRecord) {
                // 勤務中のレコードの内、休憩中のレコードを取得
                $breakRecord = BreakRecord::where('user_id', $user->id)
                    ->whereNotNull('start_time')
                    ->whereNull('end_time')
                    ->first();

                // 休憩中だった場合の処理
                if ($breakRecord) {
                    // 23:59:59に休憩終了打刻
                    $breakRecord->update(['end_time' => $midnight->toDateTimeString()]);
                }

                // 23:59:59に勤務終了打刻
                $attendanceRecord->update(['clock_out' => $midnight->toDateTimeString()]);

                // 00:00:00に勤務開始打刻
                AttendanceRecord::create([
                    'user_id' => $user->id,
                    'clock_in' => $nextDay->toDateTimeString()
                ]);

                // 休憩中だった場合の処理
                if ($breakRecord) {
                    // 00:00:00に休憩開始打刻
                    BreakRecord::create([
                        'user_id' => $user->id,
                        'attendance_record_id' => AttendanceRecord::where('user_id', $user->id)
                            ->whereNotNull('clock_in')
                            ->whereNull('clock_out')
                            ->latest('clock_in')
                            ->first()
                            ->id,
                        'start_time' => $nextDay->toDateTimeString()
                    ]);
                }
            }
        }

        // 処理完了メッセージ
        $this->info('Attendance and breaks have been handled at midnight.');
    }
}
