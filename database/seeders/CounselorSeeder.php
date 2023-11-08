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
            'name' => '九十九ゆき',
            'contact_phone' => '098-988-9188',
            'contact_email' => 'yuki.tsukumo@test.co.jp',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]];

        DB::table('counselors')->insert($param);
    }
}
