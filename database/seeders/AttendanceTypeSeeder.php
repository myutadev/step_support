<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $param = [[
            'name' => '通常勤務',
        ], [
            'name' => '遅刻',
        ], [
            'name' => '欠勤',
        ], [
            'name' => '無断欠勤',
        ], [
            'name' => '有給',
        ]];

        DB::table('attendance_types')->insert($param);
    }
}
