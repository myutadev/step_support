<?php

namespace App\Services;

use App\Repositories\AdminRepository;
use App\Repositories\AttendanceTypeRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;

use App\Domains\Attendance\DailyAdminComment;
use App\Domains\Attendance\DailyOvertime;
use App\Domains\Attendance\DailyRest;
use App\Domains\Attendance\DailyTimeSlot;
use App\Domains\Attendance\DailyUserAttendance;
use App\Domains\Attendance\TimeSlot;
use App\Models\ScheduleType;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\AttendanceType;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class TimeCardService
{
    protected $adminRepository;
    protected $attendanceTypeRepository;
    protected $userRepository;
    protected $attendanceService;
    protected $workScheduleService;

    public function __construct(
        AdminRepository $adminRepository,
        AttendanceTypeRepository $attendanceTypeRepository,
        UserRepository $userRepository,
        AttendanceService $attendanceService,
        WorkScheduleService $workScheduleService,
    ) {
        $this->adminRepository = $adminRepository;
        $this->attendanceTypeRepository = $attendanceTypeRepository;
        $this->userRepository = $userRepository;
        $this->attendanceService = $attendanceService;
        $this->workScheduleService = $workScheduleService;
    }
    /**
     * 出勤日以外の表示レコードを作成。
     * 出勤日か休日か、そのID、日付、のみを含んだオブジェクトを返す。
     *@param  \App\Models\WorkSchedule $workschedule 
     *@return array 出勤レコードが無いようのオブジェクト
     */
    // privateに変えるかも?
    public function generateNoAttendanceRecordObj(WorkSchedule $workSchedule)
    {
        return
            $curAttendanceObj = [
                'attendance_id' => "",
                'attendance_type' => "",
                'workSchedule_id' => $workSchedule->id,
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
                'admin_comment' => "",
            ];
    }

    private function isAttendedRecord(Attendance $attendance): bool
    {
        return !in_array($attendance->id, $this->attendanceService->getLeaveTypeIds());
    }

    /**
     *管理者コメントを生成する
     *DailyAdminCommentクラスを作成。
     *@param \App\Models\Attendance $attendance
     *@return DailyAdminComment タイムカード画面表示用の管理者コメントクラス
     */
    private function generateDailyAdminCommentClass(Attendance $attendance): DailyAdminComment
    {
        $curDailyAdminComment = new DailyAdminComment();
        $curAdminComments = $attendance->adminComments;


        foreach ($curAdminComments as $adminComment) {
            $curDailyAdminComment->push($adminComment);
        }

        return $curDailyAdminComment;
    }

    /**
     *Attendanceに紐づく当日の休憩レコードを保持するDailyTimeSloctを生成する
     *Attendanceに休憩レコードが複数紐づく場合もある。Attendanceが欠席系のレコードだった場合Nullオブジェクトを返す。
     *@param \App\Models\Attendance $attendancen
     *@return DailyRest 1つのAttendanceに紐づく休憩レコードを表現するクラス
     */
    private function generateDailyRestClass(Attendance $attendance): DailyRest
    {
        if (!$this->isAttendedRecord($attendance)) return new DailyRest();
        $dailyTimeSlotForRest = new DailyTimeSlot($attendance->id);
        $curRests = $attendance->rests;

        foreach ($curRests as $rest) {
            $curTimeSlot = new TimeSlot(Carbon::parse($rest->start_time), Carbon::parse($rest->end_time));
            $dailyTimeSlotForRest->push($curTimeSlot);
        }

        return new DailyRest($dailyTimeSlotForRest);
    }

    /**
     *Attendanceに紐づく当日の残業レコードを保持するDailyOvertimeSloctを生成する
     *Attendanceに残業レコードが複数紐づく場合もある。Attendanceが欠席系のレコードだった場合Nullオブジェクトを返す。
     *@param \App\Models\Attendance $attendancen
     *@return DailyOvertime 1つのAttendanceに紐づく休憩レコードを表現するクラス
     */
    private function generateDailyOvertimeClass(Attendance $attendance): DailyOvertime
    {

        if (!$this->isAttendedRecord($attendance)) return new DailyOvertime();

        $dailyTimeSlotForOvertime = new DailyTimeSlot($attendance->id);
        $curOvertimes = $attendance->overtimes;

        foreach ($curOvertimes as $overtime) {
            $curTimeSlot = new TimeSlot(Carbon::parse($overtime->start_time), Carbon::parse($overtime->end_time));
            $dailyTimeSlotForOvertime->push($curTimeSlot);
        }

        return new DailyOvertime($dailyTimeSlotForOvertime);
    }
    /**
     *出席レコードがあった場合の出席レコードオブジェクトを返すメソッド
     *
     *@param \App\Models\Attendance $attendance 
     *@return Array 1日の出席レコードが入ったDailyUserAttendanceオブジェクトのメソッドを使って、表示用レコードを取得
     */
    private function generateAttendanceRecordObj(Attendance $attendance, Workschedule $workSchedule)
    {
        $curDailyAdminComment = $this->generateDailyAdminCommentClass($attendance);
        $curDailyRest = $this->generateDailyRestClass($attendance);
        $curDailyOvertime = $this->generateDailyOvertimeClass($attendance);
        $curDailyUserAttendance = new DailyUserAttendance($attendance, $workSchedule, $curDailyOvertime, $curDailyRest, $curDailyAdminComment);
        return $curDailyUserAttendance->createAttendanceObj();
    }

    /**
     *出席日に紐づくAttendanceレコードの有無を確認し、適切な出席データを返す
     *WorkScheduleからその日付に紐づく、Attendanceレコードを取得。紐づくレコードがなければ、レコードが内容のAttendanceObjを返す。
     *レコードがあれば、Attendanceが存在する用のレコードを返す。
     *
     *@param \App\Models\Workschedule $workSchedule
     *@return Array timetableに表示する1日のレコードオブジェクトを作成
     */
    private function generateDailyAttendanceRecordObj(WorkSchedule $workSchedule)
    {
        $attendance = $workSchedule->attendances->first();

        //初期値として、出席レコードがない場合のオブジェクトを保存
        $dailyAttendanceObj = $this->generateNoAttendanceRecordObj($workSchedule);

        //出席レコードある場合には上書き
        if ($attendance) $dailyAttendanceObj = $this->generateAttendanceRecordObj($attendance, $workSchedule);

        return $dailyAttendanceObj;
    }
    /**
     *各ユーザーごとのTimecard用の月次出席表示用の配列を返す
     *1ヶ月分のデータを格納する配列monthlyAttendanceDataを用意し、1日毎にデータを作成し順にmonthlyAttendanceDataへ格納する
     *
     *@param \App\Models\Workschedule $workSchedule
     *@return Array timetableに表示する月次のレコードオブジェクトを作成
     */
    public function generateMonthlyAttendanceData($year, $month, $user_id): array
    {
        $monthlyAttendanceData = [];

        $thisMonthWorkSchedules = $this->workScheduleService->getSelectedMonthWorkSchedulesByUser($year, $month, $user_id);

        foreach ($thisMonthWorkSchedules as $workSchedule) {
            $curAttendanceObj =   $this->generateDailyAttendanceRecordObj($workSchedule);
            array_push($monthlyAttendanceData, $curAttendanceObj);
        }
        return $monthlyAttendanceData;
    }
}
