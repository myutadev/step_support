<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CounselorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $param = [[
            'name' => '佐藤花子',
            'contact_phone' => '098-988-9188',
            'contact_email' => 'hanako.sato@test.co.jp',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'company_id' => '1',
        ], [
            'name' => '鈴木真理子',
            'contact_phone' => '098-988-9188',
            'contact_email' => 'mariko.suzuki@test.co.jp',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'company_id' => '1',
        ], [
            'name' => '田中健太郎',
            'contact_phone' => '098-988-9188',
            'contact_email' => 'kentaro.tanaka@test.co.jp',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'company_id' => '1',
        ]];

        DB::table('counselors')->insert($param);
    }
}
