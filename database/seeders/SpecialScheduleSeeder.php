<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SpecialSchedule;

class SpecialScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = [
            [
                'company_id' => '1',
                'work_schedule_id' => '398',
                'schedule_type_id' => '2',
                'description' => '振替休日',
            ],
            [
                'company_id' => '1',
                'work_schedule_id' => '399',
                'schedule_type_id' => '1',
                'description' => '振替出勤',
            ],
        ];

        foreach ($schedules as $schedule) {
            SpecialSchedule::create($schedule);
        }
    }
}
