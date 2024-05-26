<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class UserDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = Faker::create();

        return [
            'id' => 1,
            'user_id' => 1,
            'beneficiary_number' => '0000110569',
            'admission_date' => $faker->dateTimeBetween($startDate = '2022-04-01', $endDate = '2024-01-01')->format('Y-m-d'),
            'discharge_date' => null,
            'is_on_welfare' => 0,
            'company_id' => 1,
            'disability_category_id' => 3,
            'residence_id' => 2,
            'counselor_id' => 1,
            'birthdate' => $faker->dateTimeBetween($startDate = '1980-04-01', $endDate = '1999-01-01')->format('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }
}
