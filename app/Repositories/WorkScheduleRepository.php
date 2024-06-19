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

    /**
     *日別出勤状況に表示させるデータを抽出する
     *companyId, $selectedDateでデータを絞る
     *
     *@param $comanyId 
     *@param $selectedDate 
     *@return Collection 
     */
    public function generateDailyAttenanceData($companyId, $selectedDate)
    {
        return  WorkSchedule::whereHas('attendances', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->with(['attendances.rests', 'attendances.overtimes', 'attendances.adminComments.admin', 'attendances.user.userDetail'])
            ->where('date', $selectedDate)->first();
    }

    /**
     *adminCommentからWorkScheduleを取得するメソッド
     *日別出勤状況の管理者コメント編集機能で使われる
     *@param AdminComment 
     *@return Collection
     */

    public function getWorkScheduleByAdminComment($adminComment)
    {
        return WorkSchedule::where('id', $adminComment->attendance->work_schedule_id)->first();
    }
}
