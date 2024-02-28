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
                'name' => '自宅',
                'contact_name' => '',
                'contact_phone' => '',
                'contact_email' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'company_id' => '1',
            ],
            [
                'name' => 'いーまーる識名',
                'contact_name' => '高橋美佳',
                'contact_phone' => '4563-33-2323',
                'contact_email' => 'mika.takahashi@test.co.jp',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'company_id' => '1',
            ],  [
                'name' => 'グループホームゆいまーる 那覇',
                'contact_name' => '渡辺直樹',
                'contact_phone' => '4563-34-2323',
                'contact_email' => 'naoki.watanabe@test.co.jp',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'company_id' => '1',
            ]

        ];

        DB::table('residences')->insert($params);
    }
}
