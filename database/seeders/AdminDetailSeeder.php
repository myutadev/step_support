<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AdminDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $hireDate = Carbon::create(2022, 2, 3);

        $param = [[
            'id' => 1,
            'admin_id' => 1,
            'emp_number' => '000001',
            'hire_date' => $hireDate->toDateString(),
            'termination_date' => null,
            'role_id' => 1,
            'company_id' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ],[
            'id' => 2,
            'admin_id' => 2,
            'emp_number' => '000002',
            'hire_date' => $hireDate->toDateString(),
            'termination_date' => null,
            'role_id' => 1,
            'company_id' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ],[
            'id' => 3,
            'admin_id' => 3,
            'emp_number' => '0000013',
            'hire_date' => $hireDate->toDateString(),
            'termination_date' => null,
            'role_id' => 2,
            'company_id' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]];

        DB::table('admin_details')->insert($param);
    }
}
