<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkSchedule>
 */
class WorkScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => fake()->unique()->date(),
            'year' => fake()->unique()->year(),
            'month' => fake()->unique()->month(),
            'day' => fake()->unique()->dayOfMonth(),
            'schedule_type_id' => 1
        ];
    }
}
