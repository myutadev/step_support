<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdminComment>
 */
class AdminCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_id' => fake()->numberBetween(1, 3),
            'admin_description' => fake()->realText(20),
            'admin_comment' => fake()->realText(70),
        ];
    }
}
