<?php

namespace App\Repositories;

use App\Models\ScheduleType;
use App\Models\WorkSchedule;
use Illuminate\Database\Eloquent\Collection;

class WorkScheduleRepository
{

    public function getWorkDayName(): string
    {
        return ScheduleType::find(1)->name;
    }
    
    public function getSelectedMonthWorkSchedulesByUser(int $year, int $month, int $user_id): Collection
    {
        return WorkSchedule::with(
            [
                'specialSchedule.schedule_type',
                'scheduleType',
                'attendances' => function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                },
                'attendances.rests',
                'attendances.overtimes',
                'attendances.adminComments.admin',
                'attendances.attendanceType'
            ]
        )
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'asc')
            ->get();
    }
    public function getAllSchedulesForMonth(int $year, int $month): Collection
    {
        $thisMonthAllSchedules =
            WorkSchedule::with(['scheduleType', 'specialSchedule'])
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'asc')
            ->get();

        return $thisMonthAllSchedules;
    }
}
