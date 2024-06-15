<?php

namespace Database\Factories;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

class AttendanceRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $clockIn = $this->faker->dateTimeBetween('-1 month', 'now');
        $clockOut = (clone $clockIn)->modify('+8 hours'); // 勤務時間を8時間と仮定

        return [
            'user_id' => User::factory(),
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
        ];
    }
}
