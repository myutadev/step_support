<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */


    public function definition(): array
    {

        $today = now()->format('Y-m-d');

        $checkInTime = $this->faker->dateTimeBetween($startDate = "{$today} 09:30:00", $endData = "{$today} 15:00:00")->format('H:i:s');
        $checkOutTime = $this->faker->dateTimeBetween($startDate = "{$today} {$checkInTime}", $endData = "{$today} 18:00:00")->format('H:i:s');
        return [
            //
            'company_id' => 1,
            'user_id' => 1,
            'attendance_type_id' => 1,
            'work_schedule_id' => 1, // これは後でcreateで上書き
            'check_in_time' => $checkInTime,
            'check_out_time' => $checkOutTime,
            'body_temp' => fake()->randomFloat(1, 35.0, 36.9),
            'work_description' => fake()->realText(20),
            'work_comment' => fake()->realText(150),
        ];
    }
}
