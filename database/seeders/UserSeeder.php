<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $params = [[
            'id' => 1,
            'last_name' => '山本',
            'first_name' => '薫平',
            'email' => 'user1@test.co.jp',
            'email_verified_at' => null,
            'password' => Hash::make('1111'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], [
            'id' => 2,
            'last_name' => '山本',
            'first_name' => '陽平',
            'email' => 'user2@test.co.jp',
            'email_verified_at' => null,
            'password' => Hash::make('1111'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], [
            'id' => 3,
            'last_name' => '仲程',
            'first_name' => '里美',
            'email' => 'user3@test.co.jp',
            'email_verified_at' => null,
            'password' => Hash::make('1111'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]];

        DB::table('users')->insert($params);
    }
}
