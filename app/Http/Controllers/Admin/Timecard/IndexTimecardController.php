<?php

namespace App\Http\Controllers\Admin\Timecard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
use App\Models\AttendanceType;
use App\Models\UserDetail;
use App\Services\MonthUserSelectorService;
use App\Services\WorkScheduleService;
use Carbon\Carbon;
use Database\Seeders\WorkScheduleSeeder;
use Illuminate\Support\Facades\Auth;


class IndexTimecardController extends Controller
{

    protected $monthUserSelectorService;
    protected $workScheduleService;


    public function __construct(MonthUserSelectorService $monthUserSelectorService, WorkScheduleService $workScheduleService)
    {
        $this->monthUserSelectorService = $monthUserSelectorService;
        $this->workScheduleService = $workScheduleService;
    }

    public function __invoke($yearmonth = null, $user_id = null)
    {
        $users = $this->monthUserSelectorService->getUsersByCompanyId();

        // アクセサを記述
        $workDayName = ScheduleType::find(1)->name;
        // Move to AttendanceType Repository 
        $leaveTypes = AttendanceType::where('name', 'LIKE', '%欠勤%')->orWhere('name', 'LIKE', '%有給%')->get();
        $leaveTypesIds = $leaveTypes->pluck('id')->toArray();

        //monthUserSelectorService
        $selectedYearMonth = $this->monthUserSelectorService->getSelectedYearMonth($yearmonth);
        $year = $selectedYearMonth["year"];
        $month = $selectedYearMonth["month"];
        $user_id = $this->monthUserSelectorService->getSelectedUserId($user_id)->id;

        // 表示データの作成
        $monthlyAttendanceData = [];

        //WorkSchedule repository
        $thisMonthWorkSchedules = $this->workScheduleService->getSelectedMonthWorkSchedulesByUser($year, $month, $user_id);

        // showMonthlyAttendanceData(MonthlyWorkSchedule)
        foreach ($thisMonthWorkSchedules as $workSchedule) {
            $curAttendance = $workSchedule->attendances->first();
            //出席レコードがない場合 -> private method? 

            if (!$curAttendance) {
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
                    // 'workday_name' => $workDayName
                ];
                array_push($monthlyAttendanceData, $curAttendanceObj);
                continue;
            }

            //出席レコードがある確認 private method
            $isAttend = !in_array($curAttendance->id, $leaveTypesIds);

            //0.DailyUserAttendanceの作成- 欠席も出席も共通で存在
            $curDailyAdminComment = new DailyAdminComment();
            $curAdminComments = $curAttendance->adminComments;

            foreach ($curAdminComments as $adminComment) {
                $curDailyAdminComment->push($adminComment);
            }

            //欠席レコードの場合Nullオブジェクトを返す
            if (!$isAttend) {
                $curDailyRest = new DailyRest();
                $curDailyOvertime = new DailyOvertime();
            }

            //1.DailyRest作成 = TimeSlotの作成, 
            $dailyTimeSlotForRest = new DailyTimeSlot($curAttendance->id);
            $curRests = $curAttendance->rests;

            foreach ($curRests as $rest) {
                $curTimeSlot = new TimeSlot(Carbon::parse($rest->start_time), Carbon::parse($rest->end_time));
                $dailyTimeSlotForRest->push($curTimeSlot);
            }

            $curDailyRest = new DailyRest($dailyTimeSlotForRest);

            //2.DailyOvertime作成 = TimeSlotの作成, 

            $dailyTimeSlotForOvertime = new DailyTimeSlot($curAttendance->id);
            $curOvertimes = $curAttendance->overtimes;

            foreach ($curOvertimes as $overtime) {
                $curTimeSlot = new TimeSlot(Carbon::parse($overtime->start_time), Carbon::parse($overtime->end_time));
                $dailyTimeSlotForOvertime->push($curTimeSlot);
            }

            $curDailyOvertime = new DailyOvertime($dailyTimeSlotForOvertime);

            $curDailyUserAttendance = new DailyUserAttendance($curAttendance, $workSchedule, $curDailyOvertime, $curDailyRest, $curDailyAdminComment);
            $curAttendanceObj =   $curDailyUserAttendance->createAttendanceObj();


            array_push($monthlyAttendanceData, $curAttendanceObj);
        }

        return view('admin.attendances.admintimecard', compact('monthlyAttendanceData', 'year', 'month', 'users', 'user_id', 'leaveTypes', 'workDayName'));
    }

    /**
     * Handle the incoming request.
     */
    // public function __invoke($yearmonth = null, $user_id = null)
    // {
    //     // Move to Admin Repository 
    //     $adminId = Auth::id();
    //     $admin = Admin::with('adminDetail')->find($adminId);
    //     $companyId = $admin->adminDetail->company_id;
    //      // Move to UseRepository   
    //     $users = User::whereHas('userDetail', function ($query) use ($companyId) {
    //         $query->where('company_id', $companyId);
    //     })->with('userDetail')->get();

    //     // アクセサを記述
    //     $workDayName = ScheduleType::find(1)->name;
    //     // Move to AttendanceType Repository 
    //     $leaveTypes = AttendanceType::where('name', 'LIKE', '%欠勤%')->orWhere('name', 'LIKE', '%有給%')->get();
    //     $leaveTypesIds = $leaveTypes->pluck('id')->toArray();


    //     //ここはトレイト?
    //     if ($yearmonth == null) {
    //         $today = Carbon::today();
    //         $year = $today->year;
    //         $month = sprintf("%02d", $today->month);
    //     } else {
    //         $yearMonthArr = explode("-", $yearmonth);
    //         $year = $yearMonthArr[0];
    //         $month = sprintf("%02d", $yearMonthArr[1]);
    //     }


    //     if ($user_id == null) {
    //         $userDetail = UserDetail::where('company_id', $companyId)->first();
    //         $user_id = $userDetail->user_id;
    //     }

    //     // 表示データの作成
    //     $monthlyAttendanceData = [];
    //     //WorkSchedule repository
    //     $thisMonthWorkSchedules = WorkSchedule::with(['specialSchedule.schedule_type', 'scheduleType', 'attendances' => function ($query) use ($user_id) {
    //         $query->where('user_id', $user_id);
    //     }, 'attendances.rests', 'attendances.overtimes', 'attendances.adminComments.admin', 'attendances.attendanceType'])->whereYear('date', $year)->whereMonth('date', $month)->orderBy('date', 'asc')->get(); // dd($thisMonthWorkSchedules);


    //     // showMonthlyAttendanceData(MonthlyWorkSchedule)
    //     foreach ($thisMonthWorkSchedules as $workSchedule) {
    //         $curAttendance = $workSchedule->attendances->first();
    //         //出席レコードがない場合 -> private method? 

    //         if (!$curAttendance) {
    //             $curAttendanceObj = [
    //                 'attendance_id' => "",
    //                 'attendance_type' => "",
    //                 'workSchedule_id' => $workSchedule->id,
    //                 'date' => $workSchedule->date,
    //                 'scheduleType' => $workSchedule->specialSchedule == null ? $workSchedule->scheduleType->name : $workSchedule->specialSchedule->schedule_type->name,
    //                 'bodyTemp' => "",
    //                 'checkin' => "",
    //                 'checkout' => "",
    //                 'is_overtime' => "",
    //                 'rest' => "",
    //                 'overtime' => "",
    //                 'duration' => "",
    //                 'workDescription' => "",
    //                 'workComment' => "",
    //                 'admin_comment' => "",
    //                 // 'workday_name' => $workDayName
    //             ];
    //             array_push($monthlyAttendanceData, $curAttendanceObj);
    //             continue;
    //         }

    //         //出席レコードがある確認 private method
    //         $isAttend = !in_array($curAttendance->id, $leaveTypesIds);

    //         //0.DailyUserAttendanceの作成- 欠席も出席も共通で存在
    //         $curDailyAdminComment = new DailyAdminComment();
    //         $curAdminComments = $curAttendance->adminComments;

    //         foreach ($curAdminComments as $adminComment) {
    //             $curDailyAdminComment->push($adminComment);
    //         }

    //         //欠席レコードの場合Nullオブジェクトを返す
    //         if (!$isAttend) {
    //             $curDailyRest = new DailyRest();
    //             $curDailyOvertime = new DailyOvertime();
    //         }

    //         //1.DailyRest作成 = TimeSlotの作成, 
    //         $dailyTimeSlotForRest = new DailyTimeSlot($curAttendance->id);
    //         $curRests = $curAttendance->rests;

    //         foreach ($curRests as $rest) {
    //             $curTimeSlot = new TimeSlot(Carbon::parse($rest->start_time), Carbon::parse($rest->end_time));
    //             $dailyTimeSlotForRest->push($curTimeSlot);
    //         }

    //         $curDailyRest = new DailyRest($dailyTimeSlotForRest);

    //         //2.DailyOvertime作成 = TimeSlotの作成, 

    //         $dailyTimeSlotForOvertime = new DailyTimeSlot($curAttendance->id);
    //         $curOvertimes = $curAttendance->overtimes;

    //         foreach ($curOvertimes as $overtime) {
    //             $curTimeSlot = new TimeSlot(Carbon::parse($overtime->start_time), Carbon::parse($overtime->end_time));
    //             $dailyTimeSlotForOvertime->push($curTimeSlot);
    //         }

    //         $curDailyOvertime = new DailyOvertime($dailyTimeSlotForOvertime);

    //         $curDailyUserAttendance = new DailyUserAttendance($curAttendance, $workSchedule, $curDailyOvertime, $curDailyRest, $curDailyAdminComment);
    //         $curAttendanceObj =   $curDailyUserAttendance->createAttendanceObj();


    //         array_push($monthlyAttendanceData, $curAttendanceObj);
    //     }

    //     return view('admin.attendances.admintimecard', compact('monthlyAttendanceData', 'year', 'month', 'users', 'user_id', 'leaveTypes', 'workDayName'));

    // }
}
