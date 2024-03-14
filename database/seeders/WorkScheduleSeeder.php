<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startDate = Carbon::create(2023, 1, 1);
        $endDate = Carbon::create(2030, 12, 31);

        $batchSize = 500; // 一度に挿入するレコードの数
        $data = []; // 挿入データを一時的に保持する配列

        while ($startDate->lte($endDate)) {
            $data[] = [
                'date' => $startDate->toDateString(),
                'year' => $startDate->year,
                'month' => $startDate->month,
                'day' => $startDate->day,
                'schedule_type_id' => $this->getScheduleTypeId($startDate),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // バッチサイズに達したらデータベースに挿入
            if (count($data) >= $batchSize) {
                DB::table('work_schedules')->insert($data);
                $data = []; // データ配列をリセット
            }

            $startDate->addDay();
        }

        // 残りのデータがあれば挿入
        if (!empty($data)) {
            DB::table('work_schedules')->insert($data);
        }
    }

    private function getScheduleTypeId($date)
    {
        return $date->isWeekend() ? 2 : 1;
    }
}
