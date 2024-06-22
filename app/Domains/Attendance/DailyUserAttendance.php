<?php

namespace App\Domains\Attendance;

use App\Models\Attendance;
use App\Models\WorkSchedule;
use App\Utils\TimeFormatter;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class DailyUserAttendance
{
    protected Attendance $attendance;
    protected WorkSchedule $workSchedule;
    protected DailyOvertime $dailyOvertime;
    protected DailyRest $dailyRest;
    protected DailyAdminComment $dailyAdminComment;


    public function __construct(Attendance $attendance, WorkSchedule $workSchedule, DailyOvertime $dailyOvertime, DailyRest $dailyRest, DailyAdminComment $dailyAdminComment)
    {
        //DailyRest,DailyOvertimeはnullの可能性もある
        $this->attendance = $attendance;
        $this->workSchedule = $workSchedule;
        $this->dailyRest = $dailyRest;
        $this->dailyOvertime = $dailyOvertime;
        $this->dailyAdminComment = $dailyAdminComment;
    }

    /**
     *出席がない日用のDailyUserAttendanceObjを作成
     *
     * @return Object
     */


    public function getAttendanceId(): int
    {
        return $this->attendance->id;
    }
    public function getAttendanceType(): string
    {
        return $this->attendance->attendanceType->name;
    }
    public function getWorkScheduleId(): int
    {
        return $this->workSchedule->id;
    }

    public function getDate(): String
    {
        return $this->workSchedule->date;
    }
    public function getScheduleType(): string
    {
        return $this->workSchedule->specialSchedule == null ? $this->workSchedule->scheduleType->name : $this->workSchedule->specialSchedule->scheduleType->name;
    }
    public function getCheckIn()
    {
        return $this->attendance->check_in_time;
    }
    public function getCheckOut()
    {
        return $this->attendance->check_out_time;
    }
    public function getBodyTemp()
    {
        return $this->attendance->body_temp;
    }
    public function getIsOvertime(): string
    {
        return $this->attendance->is_overtime === 1 ? "有" : "無";
    }
    public function getDescription()
    {
        return $this->attendance->work_description;
    }
    public function getComment()
    {
        return $this->attendance->work_comment;
    }
    public function getFullName(): string
    {
        return $this->attendance->user->getFullNameAttribute();
    }
    public function getBeneficiaryNumber(): string
    {
        return $this->attendance->user->userDetail->beneficiary_number;
    }
    public function getDailyRest(): DailyRest
    {
        return $this->dailyRest;
    }

    public function showAllDailyRestStr(): String
    {
        return $this->dailyRest->getDailyTimeSlot()->showAllTimeSlotsStr();
    }


    public function getDailyOvertime(): DailyOvertime
    {
        return $this->dailyOvertime;
    }

    public function showAllDailyOvertimeStr(): String
    {
        return $this->dailyOvertime->getDailyTimeSlot()->showAllTimeSlotsStr();
    }


    public function showAllDailyAdminCommentStr(): String
    {
        return $this->dailyAdminComment->showAllComments();
    }



    /**
     *チェックイン時間とチェックアウト時間の経過時間を取得
     *
     *@return CarbonInterval
     */

    public function showTotalBaseWorkingDuration(): CarbonInterval
    {

        $workingDuration = Carbon::parse($this->getCheckIn())->diff(Carbon::parse($this->getCheckOut()));
        return CarbonInterval::instance($workingDuration);
    }

    /**
     *休憩･残業の合計Durationを返す
     *
     *@return CarbonInterval
     */

    public function getDuration(DailyTimeSlot $dailyTimeSlot): CarbonInterval
    {
        return is_null($dailyTimeSlot) ? CarbonInterval::hour(0) : $dailyTimeSlot->sumTotalDuration();
    }
    /**
     *出勤時間･退勤時間･残業時間から1日の労働時間を算出し
     *
     *@return CarbonInterval
     */

    public function showNetWorkDuration(): CarbonInterval
    {
        $workingDuration = $this->showTotalBaseWorkingDuration();

        $dailyRest = $this->getDailyRest();
        $restDuration = $this->getDuration($dailyRest->getDailyTimeSlot());

        $dailyOvertime = $this->getDailyOvertime();
        $overtimeDuration =  $this->getDuration($dailyOvertime->getDailyTimeSlot());

        return $workingDuration->sub($restDuration)->add($overtimeDuration);
    }

    //restとovertimeのメソッドのテストの続きから
    public function createAttendanceObj(): array
    {
        return [
            'attendance_id' => $this->getAttendanceId(),
            'attendance_type' => $this->getAttendanceType(),
            'workSchedule_id' => $this->getWorkScheduleId(),
            'date' => $this->getDate(),
            'scheduleType' => $this->getScheduleType(),
            'bodyTemp' => $this->getBodyTemp(),
            'checkin' => $this->getCheckIn(),
            'checkout' => $this->getCheckOut(),
            'is_overtime' => $this->getIsOvertime(),
            'rest' => $this->showAllDailyRestStr(),
            'overtime' => $this->showAllDailyOvertimeStr(),
            'duration' => TimeFormatter::carbonIntervalToStringHours($this->showNetWorkDuration()),
            'workDescription' => $this->getDescription(),
            'workComment' => $this->getComment(),
            'admin_comment' => $this->showAllDailyAdminCommentStr(),
        ];
    }
}
