<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // 10人のメール認証済みユーザーを作成
        User::factory()->count(10)->create()->each(function ($user) use ($faker) {
            // 各ユーザーに対して10個の勤怠レコードを作成
            AttendanceRecord::factory()->count(10)->for($user)->create()->each(function ($attendanceRecord) use ($user, $faker) {
                // 50%の確率で休憩レコードを作成
                if (rand(0, 1)) {
                    // 勤務時間内に収まるように休憩開始時間と終了時間を設定
                    $start = $faker->dateTimeBetween($attendanceRecord->clock_in, $attendanceRecord->clock_out->modify('-1 hour'));
                    $end = (clone $start)->modify('+'.rand(1, 60).' minutes'); // 1分から60分の休憩

                    BreakRecord::create([
                        'user_id' => $user->id,
                        'attendance_record_id' => $attendanceRecord->id,
                        'start_time' => $start,
                        'end_time' => $end,
                    ]);
                }
            });
        });
    }
}
