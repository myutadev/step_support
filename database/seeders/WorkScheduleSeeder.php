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

        while ($startDate->lte($endDate)) {
            DB::table('work_schedules')->insert([
                'date' => $startDate->toDateString(),
                'year' => $startDate->year,
                'month' => $startDate->month,
                'day' => $startDate->day,
                'schedule_type_id' => $this->getScheduleTypeId($startDate),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $startDate->addDay();
        }
    }

    private function getScheduleTypeId($date)
    {
        return $date->isWeekend() ? 2 : 1;
    }
}
