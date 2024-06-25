<?php

namespace App\Services;

use App\Models\WorkSchedule;
use App\Repositories\AttendanceRepository;
use App\Repositories\OvertimeRepository;
use App\Repositories\RestRepository;
use App\Repositories\UserRepository;
use App\Repositories\WorkScheduleRepository;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Auth;

class UserTimecardService
{
    protected $userRepository;
    protected $workScheduleRepository;
    protected $attendanceRepository;
    protected $restRepository;
    protected $overtimeRepository;
    protected $monthUserSelectorService;



    public function __construct(
        UserRepository $userRepository,
        WorkScheduleRepository $workScheduleRepository,
        AttendanceRepository $attendanceRepository,
        RestRepository $restRepository,
        OvertimeRepository $overtimeRepository,
        MonthUserSelectorService $monthUserSelectorService,


    ) {
        $this->userRepository = $userRepository;
        $this->workScheduleRepository = $workScheduleRepository;
        $this->attendanceRepository = $attendanceRepository;
        $this->restRepository = $restRepository;
        $this->overtimeRepository = $overtimeRepository;
        $this->monthUserSelectorService = $monthUserSelectorService;
    }

    public function showTimecard($yearmonth = null)
    {
        $user = Auth::user();

        $thisMonthWorkSchedules = $this->generateSelecedMonthSchedule($yearmonth, $user);
        $monthlyAttendanceData = [];

        foreach ($thisMonthWorkSchedules as $workSchedule) {
            $curAttendance = $workSchedule->attendances->first();

            if (!$curAttendance) {
                $curAttendObj = $this->generateNoAttendanceRecordObj($workSchedule);
                array_push($monthlyAttendanceData, $curAttendObj);
                continue;
            }
        }
    }

    private function generateSelecedMonthSchedule($yearmonth, $user)
    {
        $yearmonthObj = $this->monthUserSelectorService->getSelectedYearMonth($yearmonth);
        $year = $yearmonthObj['year'];
        $month = $yearmonthObj['month'];

        return  $this->workScheduleRepository->getSelectedMonthWorkSchedulesByUser($year, $month, $user->id);
    }

    private function generateNoAttendanceRecordObj(WorkSchedule $workSchedule)
    {
        return
            [
                'date' => $workSchedule->date,
                'scheduleType' => $workSchedule->specialSchedule == null ? $workSchedule->scheduleType->name : $workSchedule->specialSchedule->schedule_type->name,
                'bodyTemp' => "",
                'checkin' => "",
                'checkout' => "",
                'is_overtime' => "",
                'rest' => "",
                'overtime' => "",
                'duration' => "",
                'workDescription' => "",
                'workComment' => "",
            ];
    }

    private function generateRestTimeString($curRests)
    {
        $restTimes = [];
        foreach ($curRests as $rest) {
            $restTimes[] = Carbon::parse($rest->start_time)->format('H:i') . '-' . Carbon::parse($rest->end_time)->format('H:i');
        }
        return implode("<br>", $restTimes);
    }
    //public 
    private function generateWorkDurationInterval($curAttendance)
    {
        $workDurationInterval = $this->generateBaseWorkDurationInterval($curAttendance);
        $overTimeInterval = $this->generateOvertimeInterval($curAttendance);
        $restInterval = $this->generateRestInterval($curAttendance->rests);
        return $workDurationInterval->add($overTimeInterval)->sub($restInterval);
    }

    private function generateBaseWorkDurationInterval($curAttendance)
    {
        $checkInTimeForCalc = Carbon::parse($curAttendance->check_in_time);
        $checkOutTimeForCalc = Carbon::parse($curAttendance->check_out_time);
        $baseTimeForIn = Carbon::parse($checkInTimeForCalc->format('Y-m-d') . ' 10:00:00');
        $baseTimeForOut = Carbon::parse($checkOutTimeForCalc->format('Y-m-d') . ' 15:00:00');
        $isOvertime = $curAttendance->is_overtime;

        //出勤時間の切り上げ
        if ($checkInTimeForCalc->lt($baseTimeForIn)) {
            $checkInTimeForCalc->hour(10)->minute(0)->second(0);
        } else {
            $checkInTimeForCalc->ceilMinute(15);
        }

        //退勤時間の切り下げ 残業なし(isOvertime=0) かつ 15時以降の打刻であれば
        if ($checkOutTimeForCalc->gt($baseTimeForOut) && $isOvertime == 0) {
            $checkOutTimeForCalc->hour(15)->minute(0)->second(0);
        } else {
            $checkOutTimeForCalc->floorminute(15);
        }

        $workDuration = $checkInTimeForCalc->diff($checkOutTimeForCalc);
        return CarbonInterval::instance($workDuration);
    }

    private function generateRestInterval($curRests)
    {
        $totalRestDuration = CarbonInterval::seconds(0); // 0秒で初期化

        foreach ($curRests as $rest) {
            $restStart = Carbon::parse($rest->start_time);
            $restEnd = Carbon::parse($rest->end_time);
            $restDuration = $restStart->floorminute(15)->diff($restEnd->ceilminute(15));

            $totalRestDuration = $totalRestDuration->add($restDuration);
        }
        return CarbonInterval::instance($totalRestDuration);
    }

    private function generateOvertimeInterval($curAttendance)
    {
        $curOvertime = $curAttendance->overtimes->first();

        if ($curOvertime == null) {
            return  CarbonInterval::seconds(0);
        } else {
            $overtimeStart = Carbon::parse($curOvertime->start_time)->ceilMinute(15);
            $overtimeEnd = Carbon::parse($curOvertime->end_time)->floorMinute(15);
            $overtimeDuration = $overtimeStart->diff($overtimeEnd);
        }

        return CarbonInterval::instance($overtimeDuration);
    }

    private function getOvertimeStr($curAttendance)
    {
        if ($curAttendance->is_overtime === 1) {
            return  "有";
        } else {
            return "無";
        }
    }

    private function isAttend($curAttendance)
    {
        return  $curAttendance->attendance_type_id == 1 || $curAttendance->attendance_type_id == 2;
    }

    private function generateAttendanceRecordObj($workSchedule)
    {
        $curAttendance = $workSchedule->attendances->first();

        $restTimeString = $this->generateRestTimeString($curAttendance->rests);
        $curOvertime = $curAttendance->overtimes->first();
        $workDurationInterval = $this->generateWorkDurationInterval($curAttendance);;
        $is_overtime_str = $this->getOvertimeStr($curAttendance);
        $isAttend = $this->isAttend($curAttendance);

        return [
            'date' => $workSchedule->date,
            'scheduleType' => $workSchedule->specialSchedule == null ? $workSchedule->scheduleType->name : $workSchedule->specialSchedule->schedule_type->name,
            'bodyTemp' => $curAttendance->body_temp,
            'checkin' => $isAttend ? Carbon::parse($curAttendance->check_in_time)->format('H:i') : "",
            'checkout' => $curAttendance->check_out_time == null ? "" : Carbon::parse($curAttendance->check_out_time)->format('H:i'),
            'is_overtime' => $isAttend ? $is_overtime_str : "",
            'rest' => $restTimeString,
            'overtime' => $curOvertime == null ? "" : Carbon::parse($curOvertime->start_time)->format('H:i') . '-' . Carbon::parse($curOvertime->end_time)->format('H:i'),
            'duration' => $curAttendance->is_overtime ? $workDurationInterval->format('%H:%I:%S') : "",
            'workDescription' => $isAttend ? $curAttendance->work_description : "欠勤/有給休暇",
            'workComment' => $curAttendance->work_comment,
        ];
    }

    public function generateMonthlyAttendanceData($yearmonth, $user)
    {
        $monthlyAttendanceData = [];

        $thisMonthWorkSchedules = $this->generateSelecedMonthSchedule($yearmonth, $user);
        foreach ($thisMonthWorkSchedules as $workSchedule) {

            $curAttendance = $workSchedule->attendances->first();

            //curAttenddanceがNull→まだ出勤されてない場合
            if (!$curAttendance) {
                $curAttendObj = $this->generateNoAttendanceRecordObj($workSchedule);
                array_push($monthlyAttendanceData, $curAttendObj);
            } else {
                $curAttendObj = $this->generateAttendanceRecordObj($workSchedule);
                array_push($monthlyAttendanceData, $curAttendObj);
            }
        }
        return $monthlyAttendanceData;
    }
}
