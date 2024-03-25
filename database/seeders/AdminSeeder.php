<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $param = [[
            'id' => 1,
            'last_name' => '花村',
            'first_name' => '昭良',
            'email' => 'admin1@test.co.jp',
            'email_verified_at' => null,
            'password' => Hash::make('1111'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], [
            'id' => 2,
            'last_name' => '花村',
            'first_name' => '直子',
            'email' => 'admin2@test.co.jp',
            'email_verified_at' => null,
            'password' => Hash::make('1111'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], [
            'id' => 3,
            'last_name' => '花村',
            'first_name' => 'そら',
            'email' => 'admin3@test.co.jp',
            'email_verified_at' => null,
            'password' => Hash::make('1111'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]];

        DB::table('admins')->insert($param);
    }
}
