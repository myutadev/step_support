<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResidenceSeeder extends Seeder
{
    /** 
     * Run the database seeds.
     */
    public function run(): void
    {
        $params = [
            [
                'name' => '実家',
                'contact_name' => '',
                'contact_phone' => '',
                'contact_email' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'グループホーム:幸せ',
                'contact_name' => '夏油傑',
                'contact_phone' => '4563-33-2323',
                'contact_email' => 'suguru.geto@test.co.jp',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]

        ];

        DB::table('residences')->insert($params);
    }
}
