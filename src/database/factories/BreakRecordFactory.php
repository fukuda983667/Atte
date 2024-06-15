<?php

namespace Database\Factories;

use App\Models\BreakRecord;
use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

class BreakRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $start = $this->faker->dateTime();
        $end = (clone $start)->modify('+'.rand(1, 60).' minutes'); // 1分から60分の休憩

        return [
            'user_id' => User::factory(),
            'attendance_record_id' => AttendanceRecord::factory(),
            'start_time' => $start,
            'end_time' => $end,
        ];
    }
}
