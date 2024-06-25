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
            'id' => '1',
            'name' => '通常勤務',
        ], [
            'id' => '2',
            'name' => '遅刻',
        ], [
            'id' => '3',
            'name' => '欠勤',
        ], [
            'id' => '4',
            'name' => '無断欠勤',
        ], [
            'id' => '5',
            'name' => '有給',
        ]];

        DB::table('attendance_types')->insert($param);
    }
}
