<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class UserDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $params = [[
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
        ], [
            'id' => 2,
            'user_id' => 2,
            'beneficiary_number' => '0000029918',
            'admission_date' => $faker->dateTimeBetween($startDate = '2022-04-01', $endDate = '2024-01-01')->format('Y-m-d'),
            'discharge_date' => null,
            'is_on_welfare' => 0,
            'company_id' => 1,
            'disability_category_id' => 1,
            'residence_id' => 3,
            'counselor_id' => 3,
            'birthdate' => $faker->dateTimeBetween($startDate = '1980-04-01', $endDate = '1999-01-01')->format('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),

        ], [
            'id' => 3,
            'user_id' => 3,
            'beneficiary_number' => '0000085225',
            'admission_date' => $faker->dateTimeBetween($startDate = '2022-04-01', $endDate = '2024-01-01')->format('Y-m-d'),
            'discharge_date' => null,
            'is_on_welfare' => 0,
            'company_id' => 1,
            'disability_category_id' => 1,
            'residence_id' => 1,
            'counselor_id' => 2,
            'birthdate' => $faker->dateTimeBetween($startDate = '1980-04-01', $endDate = '1999-01-01')->format('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]];
        DB::table('user_details')->insert($params);
    }
}
