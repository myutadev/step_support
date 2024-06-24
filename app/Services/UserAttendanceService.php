<?php

namespace App\Services;

use App\Models\Attendance;
use App\Repositories\AttendanceRepository;
use App\Repositories\OvertimeRepository;
use App\Repositories\RestRepository;
use App\Repositories\UserRepository;
use App\Repositories\WorkScheduleRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAttendanceService
{
    protected $userRepository;
    protected $workScheduleRepository;
    protected $attendanceRepository;
    protected $restRepository;
    protected $overtimeRepository;



    public function __construct(
        UserRepository $userRepository,
        WorkScheduleRepository $workScheduleRepository,
        AttendanceRepository $attendanceRepository,
        RestRepository $restRepository,
        OvertimeRepository $overtimeRepository,

    ) {
        $this->userRepository = $userRepository;
        $this->workScheduleRepository = $workScheduleRepository;
        $this->attendanceRepository = $attendanceRepository;
        $this->restRepository = $restRepository;
        $this->overtimeRepository = $overtimeRepository;
    }

    private function getWorkScheduleToday()
    {
        $userId = $this->userRepository->getCurrentUserId();
        $today = Carbon::today();

        return  $this->workScheduleRepository->getWorkScheduleByUserIdAndDate($userId, $today);
    }


    public function getUserAttendanceToday()
    {
        $workSchedule = $this->getWorkScheduleToday();
        return $workSchedule->attendances->first();
    }

    public function generateCheckInData()
    {
        $attendance = $this->getUserAttendanceToday();
        $workSchedule = $this->getWorkScheduleToday();
        return  [
            'attendance_id' => $attendance->id,
            'type' => '出勤',
            'dateTime' => $workSchedule->date . ' ' .  Carbon::parse($attendance->check_in_time)->format('H:i'),
            'is_overtime' => "",
            'body_temp' => $attendance->body_temp,
            'work_description' => "",
            'work_comment' => "",
            "edit_button" => "",
        ];
    }

    public function addCheckInData(&$attendanceArray)
    {
        $checkIn = $this->generateCheckInData();
        array_unshift($attendanceArray, $checkIn);
    }

    public function generateRestStartData($rest)
    {
        $attendance = $this->getUserAttendanceToday();
        $workSchedule = $this->getWorkScheduleToday();
        return  [
            'attendance_id' => $attendance->id,
            'type' => '休憩開始',
            'dateTime' => $workSchedule->date . ' ' .  Carbon::parse($rest->start_time)->format('H:i'),
            'is_overtime' => "",
            'body_temp' => "",
            'work_description' => "",
            'work_comment' => "",
            "edit_button" => "",
        ];
    }
    public function generateRestEndData($rest)
    {
        $attendance = $this->getUserAttendanceToday();
        $workSchedule = $this->getWorkScheduleToday();

        return [
            'attendance_id' => $attendance->id,
            'type' => '休憩終了',
            'dateTime' => $workSchedule->date . ' ' .  Carbon::parse($rest->end_time)->format('H:i'),
            'is_overtime' => "",
            'body_temp' => "",
            'work_description' => "",
            'work_comment' => "",
            "edit_button" => "",
        ];
    }
    public function addRestData($rests, &$attendanceArray)
    {
        if (!$rests) return;

        foreach ($rests as $rest) {
            $restStart = $this->generateRestStartData($rest);
            array_unshift($attendanceArray, $restStart);

            if ($rest->end_time == "") continue;

            $restEnd = $this->generateRestEndData($rest);
            array_unshift($attendanceArray, $restEnd);
        }
    }


    private function getOvertimeStr()
    {
        return $this->attendanceRepository->getOvertimeStr();
    }

    public function generateCheckOut()
    {
        $is_overtime_str = $this->getOvertimeStr();
        $attendance = $this->getUserAttendanceToday();
        $workSchedule = $this->getWorkScheduleToday();
        return [
            'attendance_id' => $attendance->id,
            'type' => '退勤',
            'dateTime' => $workSchedule->date . ' ' .  Carbon::parse($attendance->check_out_time)->format('H:i'),
            'is_overtime' => $is_overtime_str,
            'body_temp' => "",
            'work_description' => $attendance->work_description,
            'work_comment' => $attendance->work_comment,
            "edit_button" => 1,
        ];
    }

    public function addCheckOutData($attendance, &$attendanceArray)
    {
        if ($attendance->check_out_time == "") return;
        $checkOut = $this->generateCheckOut();
        array_unshift($attendanceArray, $checkOut);
    }

    public function generateOvertimeStart($overtime)
    {
        $attendance = $this->getUserAttendanceToday();
        $workSchedule = $this->getWorkScheduleToday();
        return  [
            'attendance_id' => $attendance->id,
            'type' => '残業開始',
            'dateTime' => $workSchedule->date . ' ' .  Carbon::parse($overtime->start_time)->format('H:i'),
            'is_overtime' => "",
            'body_temp' => "",
            'work_description' => "",
            'work_comment' => "",
            "edit_button" => "",
        ];
    }
    public function generateOvertimeEnd($overtime)
    {
        $attendance = $this->getUserAttendanceToday();
        $workSchedule = $this->getWorkScheduleToday();
        return [
            'attendance_id' => $attendance->id,
            'type' => '残業終了',
            'dateTime' => $workSchedule->date . ' ' .  Carbon::parse($overtime->end_time)->format('H:i'),
            'is_overtime' => "",
            'body_temp' => "",
            'work_description' => "",
            'work_comment' => "",
            "edit_button" => "",
        ];
    }

    public function addOvertimeData($overtimes, &$attendanceArray)
    {
        if (!$overtimes) return;

        foreach ($overtimes as $overtime) {
            $overtimeStart = $this->generateOvertimeStart($overtime);
            array_unshift($attendanceArray, $overtimeStart);

            if ($overtime->end_time == "") continue;
            $overtimeEnd = $this->generateOvertimeEnd($overtime);
            array_unshift($attendanceArray, $overtimeEnd);
        }
    }

    public function generateAttendanceArray($attendance)
    {
        $attendanceArray = [];
        if (!$attendance) return [];

        $this->addCheckInData($attendanceArray);
        $this->addRestData($attendance->rests, $attendanceArray);
        $this->addOvertimeData($attendance->overtimes, $attendanceArray);
        $this->addCheckOutData($attendance, $attendanceArray);

        return $attendanceArray;
    }

    private function createAttendance()
    {
        return $this->attendanceRepository->create();
    }

    public function checkIn(Request $request)
    {
        $attendance =  $this->createAttendance();
        $attendance->user_id = $request->user()->id;
        $user = $request->user();
        $userDetail = $user->userDetail;

        $attendance->company_id = $userDetail->company_id;
        $attendance->check_in_time = Carbon::now()->toTimeString();

        //10時以降のcheck-in→遅刻 basetimeを設定
        $baseCheckInTime = Carbon::parse('10:00:00');
        if (Carbon::now()->gt($baseCheckInTime)) {
            $attendance->attendance_type_id = 2;
        } else {
            $attendance->attendance_type_id = 1;
        }

        $attendance->work_schedule_id = $this->workScheduleRepository->getWorkScheduleIdToday();

        $attendance->body_temp = $request->body_temp;
        $attendance->save();
    }

    public function checkOut(Request $request)
    {
        $userId = $request->user()->id;
        $workSchedule = $this->workScheduleRepository->getWorkScheduleTodayByUserId($userId);
        $attendance = $workSchedule->attendances->first();
        $this->updateCheckOutInfoToAttendance($request, $attendance);

        // 退勤時間が12時15分を超えている場合は自動で休憩時間をつける
        // 休憩開始時間のロジック a. 12時前出勤 = 12時から休憩  or  b.12~13時出勤 or c.13時以降出勤 
        // 休憩終了時間のロジック a. 12~13時までに退勤 = 退勤時間そのまま記録 b. 13時以降に退勤 = 13時に
        // 通常:12時前に出勤→ 13時以降退勤 :一律:12:00~13:00休憩
        // イレギュラー: 12時~13時に出勤: 1. 退勤13時以降 → 出勤時間 15分切り上げた時間~13時まで休憩 2. 退勤13時までに退勤

        $carbonChecnkInTime = Carbon::parse($attendance->check_in_time);
        $carbonCheckOutTime = Carbon::parse($attendance->check_out_time);

        if ($carbonChecnkInTime->hour < 12) {
            //13:00以降であれば 現在時刻の15分切り下げ から - 13:00
            if ($carbonCheckOutTime->hour >= 13) {
                //昼休憩:休憩開始 = 12:00 , 休憩終了 = 13:00
                $this->createLunchBreak($attendance);
            } elseif ($carbonCheckOutTime->hour >= 12) {
                // 昼休憩中の退勤:休憩開始 = 12:00 , 休憩終了 = 退勤終了時間
                $this->creatLunchBreakExit($attendance);
            } else {
                //休憩なし
            }
        } elseif ($carbonChecnkInTime->hour === 12) {
            $this->createLunchBreakCheckIn($attendance);
        } else {
            //休憩なし
        }
    }


    private function updateCheckOutInfoToAttendance(Request $request, $attendance)
    {
        $attendance->check_out_time = Carbon::now()->toTimeString();
        $attendance->work_description = $request->work_description;
        $attendance->work_comment = $request->work_comment;
        $attendance->is_overtime = $request->is_overtime;
        $attendance_id = $attendance->id;
        $attendance->update();
    }

    private function createLunchBreak($attendance)
    {
        $rest = $this->restRepository->create();
        $rest->attendance_id = $attendance->id;
        $rest->start_time = "12:00:00";
        $rest->end_time = "13:00:00";
        $rest->save();
    }

    private function creatLunchBreakExit($attendance)
    {
        $rest = $this->restRepository->create();
        $rest->attendance_id = $attendance->id;
        $rest->start_time = "12:00:00";
        $rest->end_time = Carbon::now()->toTimeString();
        $rest->save();
    }
    /**
     *12時代の昼休み中に出勤を押した場合に自動で生成される休憩レコード
     *12時時代に出勤、13時前に退勤した場合(ほとんど起きない)→休憩開始12時、休憩終了=退勤終了。12時代出勤、13時以降退勤→12:00休憩開始、13:00休憩終了
     *
     *@param $attendance 休憩と紐づく出席レコード
     *@return void 休憩レコードを生成して保存するのみ
     */
    private function createLunchBreakCheckIn($attendance)
    {
        $rest = $this->restRepository->create();
        $rest->attendance_id = $attendance->id;
        $rest->start_time = $attendance->check_in_time;
        $carbonCheckOutTime = Carbon::parse($attendance->check_out_time);

        if ($carbonCheckOutTime->hour === 12) {
            // 12時台 rest_start = check in time  休憩終了=チェックアウトタイム
            $rest->end_time = Carbon::now()->toTimeString();
            $rest->save();
        } else {
            // after 13 out rest_start = check in time rest_end= 13:00  
            $rest->end_time = "13:00:00";
            $rest->save();
        }
    }

    public function restStart()
    {
        $rest = $this->restRepository->create();
        $userId = Auth::id();

        $workSchedule = $this->workScheduleRepository->getWorkScheduleTodayByUserId($userId);
        $attendance = $workSchedule->attendances->first();

        $rest->attendance_id = $attendance->id;
        $rest->start_time = Carbon::now()->toTimeString();
        $rest->save();
    }

    public function restEnd()
    {
        $userId = Auth::id();

        $workSchedule = $this->workScheduleRepository->getWorkScheduleTodayByUserId($userId);
        $attendance = $workSchedule->attendances->first();
        $rest = $attendance->rests->where('end_time', null)->first();
        $rest->end_time = Carbon::now()->toTimeString();
        $rest->update();
    }

    public function overtimeStart()
    {
        $overtime = $this->overtimeRepository->create();
        $userId = Auth::id();
        $workSchedule  = $this->workScheduleRepository->getWorkScheduleTodayByUserId($userId);
        $attendance = $workSchedule->attendances->first();

        $overtime->attendance_id = $attendance->id;
        $overtime->start_time = Carbon::now()->toTimeString();
        $overtime->save();
    }

    public function overtimeEnd()
    {
        $userId = Auth::id();
        $workSchedule  = $this->workScheduleRepository->getWorkScheduleTodayByUserId($userId);
        $attendance = $workSchedule->attendances->first();
        $overtime = $attendance->overtimes->where('end_time', null)->first();
        $overtime->end_time = Carbon::now()->toTimeString();
        $overtime->update();
    }

    public function updateWorkComment(Request $request, Attendance $attendance)
    {
        $attendance->work_description = $request->work_description;
        $attendance->work_comment = $request->work_comment;
        $attendance->update();
    }
}
