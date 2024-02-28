<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $param = [[
            'name' => '株式会社はじめのいっぽ A型',
            'contact_name' => '花村昭良',
            'contact_phone' => '098-911-0188',
            'contact_email' => 'hanamu876@gmail.com'

        ], [
            'name' => '株式会社はじめのいっぽ B型',
            'contact_name' => '花村昭良',
            'contact_phone' => '098-911-0188',
            'contact_email' => 'hanamu876@gmail.com'

        ]];

        DB::table('companies')->insert($param);
    }
}
