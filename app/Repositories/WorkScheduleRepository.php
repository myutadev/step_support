<?php

namespace App\Repositories;

use App\Models\ScheduleType;
use App\Models\SpecialSchedule;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class WorkScheduleRepository
{

    public function getWorkDayName(): string
    {
        return ScheduleType::find(1)->name;
    }

    /**
     *開所日登録時に表示させる選択肢用。全てのスケジュールタイプを取得
     *
     *@return  Collection ScheduleTypeモデルからall()で取得した値
     */
    public function getAllScheduleType()
    {
        return ScheduleType::all();
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

    /**
     *attendanceからWorkScheduleを取得するメソッド
     *日別出勤状況の管理者コメント編集機能で使われる
     *@param Attendance 
     *@return Collection
     */

    public function getWorkScheduleByAttendance($attendance)
    {
        return WorkSchedule::where('id', $attendance->work_schedule_id)->first();
    }

    public function getWorkScheduleById($id)
    {
        return WorkSchedule::find($id);
    }

    public function createSpecialSchedule()
    {
        return new SpecialSchedule();
    }

    public function getSpecialScheduleById($id)
    {
        return SpecialSchedule::find($id);
    }
    public function getWorkScheduleBySpecialScheduleId($id)
    {
        $special_sched = SpecialSchedule::with('work_schedule')->where('id', $id)->first();
        return $special_sched->work_schedule;
    }

    public function getWorkScheduleByUserIdAndDate($userId, $date)
    {
        return WorkSchedule::with([
            'attendances' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            },
            'attendances.rests',
            'attendances.overtimes',
        ])->where('date', $date)->first();
    }

    public function getWorkScheduleIdToday()
    {
        $today = Carbon::today();
        $workSchedule = WorkSchedule::where('date', $today)->first();
        return $workSchedule->id;
    }

    public function getWorkScheduleTodayByUserId($userId)
    {
        $today = Carbon::today();
        return WorkSchedule::with([
            'attendances' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }
        ])->where('date', $today)->first();
    }

}
