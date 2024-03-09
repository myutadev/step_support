<?php

namespace App\Exports;

use App\Models\Admin;
use App\Models\User;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use function PHPUnit\Framework\isEmpty;

class AttendanceExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $yearmonth;

    public function __construct($yearmonth = null)
    {
        $this->yearmonth = $yearmonth;
    }



    public function collection()
    {
        $monthlyAttendanceData = collect([]);

        $adminId = Auth::id();
        $admin = Admin::with('adminDetail')->find($adminId);
        $companyId = $admin->adminDetail->company_id;
        // $users = User::whereHas('userDetail', function ($query) use ($companyId) {
        //     $query->where('company_id', $companyId);
        // })->with('userDetail')->get();

        if ($this->yearmonth == null) {
            $today = Carbon::today();
            $year = $today->year;
            $month = sprintf("%02d", $today->month);
        } else {
            $yearMonthArr = explode("-", $this->yearmonth);
            $year = $yearMonthArr[0];
            $month = sprintf("%02d", $yearMonthArr[1]);
        }

        $thisMonthWorkSchedules = WorkSchedule::with(['specialSchedule.schedule_type',  'scheduleType', 'attendances.rests', 'attendances.overtimes', 'attendances.adminComments.admin', 'attendances.user.userDetail'])->whereYear('date', $year)->whereMonth('date', $month)->orderBy('date', 'asc')->get(); // dd($thisMonthWorkSchedules);
        //退所日なし or 退所日日付が選択月以上を含める:当月まで在籍していた人はデータを含めるべき ex 2024-01-13,2024-02-04, / selected 202
        $activeUsers = User::whereHas('userDetail', function ($query) use ($year, $month) {
            $query->Where('discharge_date', null)
                ->orWhere(function ($query) use ($year, $month) {
                    $query->whereYear('discharge_date', '>', $year);
                })
                ->orWhere(function ($query) use ($year, $month) {
                    $query->whereYear('discharge_date', '>=', $year)->whereMonth('discharge_date', '>=', $month);
                });
        })->with('userDetail')->get();

        // dd($activeUsers);

        foreach ($thisMonthWorkSchedules as $workSchedule) {
            $curAttendances = $workSchedule->attendances;
            //if(!$curattendances)-> すべてのユーザーに対して空のデータを作成
            if ($curAttendances->isEmpty()) {

                //アクティブなユーザーをすべてループさせる->退所日なし or 退所日日付が選択月を以上
                foreach ($activeUsers as $activeUser) {
                    $curAttendanceObj = [
                        'date' => $workSchedule->date,
                        'scheduleType' => $workSchedule->specialSchedule == null ? $workSchedule->scheduleType->name : $workSchedule->specialSchedule->schedule_type->name,
                        'user_name' => $activeUser->full_name,
                        'beneficiary_number' => $activeUser->userDetail->beneficiary_number,
                        'checkin' => "",
                        'checkout' => "",
                        'is_overtime' => "",
                        'rest' => "",
                        'overtime' => "",
                        'duration' => "",
                        'workDescription' => "",
                        'workComment' => "",
                        'admin_description' => "",
                        'admin_comment' => "",
                        'admin_names' => "",
                    ];
                    // dd($curAttendanceObj);
                    $monthlyAttendanceData->push($curAttendanceObj);
                }
            } else {
                foreach ($curAttendances as $curAttendance) {

                    $curRests = $curAttendance->rests;
                    //休憩は複数回入る可能性あり。
                    $restTimes = [];
                    foreach ($curRests as $rest) {
                        $restTimes[] = Carbon::parse($rest->start_time)->format('H:i') . '-' . Carbon::parse($rest->end_time)->format('H:i');
                    }
                    $restTimeString = implode("<br>", $restTimes);

                    $curOvertime = $curAttendance->overtimes->first();

                    //ここから1日の勤務時間の計算 1. 出勤 10時以前→10時、10時以降→15分単位で切り上げ
                    $checkInTimeForCalc = Carbon::parse($curAttendance->check_in_time);
                    $checkOutTimeForCalc = Carbon::parse($curAttendance->check_out_time);
                    $baseTimeForIn = Carbon::parse($checkInTimeForCalc->format('Y-m-d') . ' 10:00:00');
                    $baseTimeForOut = Carbon::parse($checkInTimeForCalc->format('Y-m-d') . ' 15:00:00');

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

                    $totalRestDuration = CarbonInterval::seconds(0); // 0秒で初期化

                    foreach ($curRests as $rest) {
                        $restStart = Carbon::parse($rest->start_time);
                        $restEnd = Carbon::parse($rest->end_time);
                        $restDuration = $restStart->floorminute(15)->diff($restEnd->ceilminute(15));

                        $totalRestDuration = $totalRestDuration->add($restDuration);
                    }
                    //残業代:なければ 0のcarboninterval,あれば計算する。

                    if ($curOvertime == null) {
                        $overtimeDuration = CarbonInterval::seconds(0);
                    } else {
                        $overtimeStart = Carbon::parse($curOvertime->start_time)->ceilMinute(15);
                        $overtimeEnd = Carbon::parse($curOvertime->end_time)->floorMinute(15);
                        $overtimeDuration = $overtimeStart->diff($overtimeEnd);
                    }

                    // duration - 休憩の合計 + 残業の時間
                    $workDuration = $checkInTimeForCalc->diff($checkOutTimeForCalc);
                    $workDurationInterval = CarbonInterval::instance($workDuration);
                    $overTimeInterval = CarbonInterval::instance($overtimeDuration);
                    $restInterval = CarbonInterval::instance($totalRestDuration);
                    $workDurationInterval = $workDurationInterval->add($overTimeInterval)->sub($restInterval);
                    // dd($workDurationInterval);
                    // if ($curAttendance->is_overtime === 1) {
                    //     $is_overtime_str = "有";
                    // } else {
                    //     $is_overtime_str = "無";
                    // }

                    $admin_comments = [];

                    // 複数のadminコメントをつなげて1つのテキストにする 各種配列を作$restTimeString = implode("<br>", $restTimes);
                    $curAdminComments = $curAttendance->adminComments;
                    $num = 1;

                    $admin_descriptions = [];
                    $admin_comments = [];
                    $admin_names = [];


                    foreach ($curAdminComments as $curAdminComment) {
                        // dd(isset($curAdminComment->admin_description));

                        $admin_descriptions[] = !isset($curAdminComment->admin_description) ? "" : "[" . $num . "]" . $curAdminComment->admin_description;
                        $admin_comments[] = !isset($curAdminComment->admin_description) ? "" : "[" . $num . "]" . $curAdminComment->admin_comment;
                        $admin_names[] = !isset($curAdminComment->admin_description) ? "" : "[" . $num . "]" .  $curAdminComment->admin->full_name;
                        $num++;
                    }

                    $admin_description = implode("", $admin_descriptions);
                    $admin_comment = implode("", $admin_comments);
                    $admin_name = implode("", $admin_names);


                    $curAttendanceObj = [
                        'date' => $workSchedule->date,
                        'scheduleType' => $workSchedule->specialSchedule == null ? $workSchedule->scheduleType->name : $workSchedule->specialSchedule->schedule_type->name,
                        'user_name' => $curAttendance->user->full_name,
                        'beneficiary_number' => $curAttendance->user->userDetail->beneficiary_number,
                        'checkin' => Carbon::parse($curAttendance->check_in_time)->format('H:i'),
                        'checkout' => $curAttendance->check_out_time == null ? "" : Carbon::parse($curAttendance->check_out_time)->format('H:i'),
                        'rest' => $restTimeString,
                        // 'is_overtime' => $is_overtime_str,
                        'overtime' => $curOvertime == null ? "" : Carbon::parse($curOvertime->start_time)->format('H:i') . '-' . Carbon::parse($curOvertime->end_time)->format('H:i'),
                        // 'duration' => $workDurationInterval->format('%H:%I:%S'),
                        'workDescription' => $curAttendance->work_description,
                        'workComment' => $curAttendance->work_comment,
                        'admin_description' => $admin_description,
                        'admin_comment' => $admin_comment,
                        'admin_names' => $admin_name,
                    ];
                    $monthlyAttendanceData->push($curAttendanceObj);
                }
            }
        }

        $sortedData =  $monthlyAttendanceData->sortBy('beneficiary_number');
        return $sortedData;
    }

    public function headings(): array
    {
        return [
            'date', 'schedule_type', 'user_name', 'beneficiary_number', 'attendance', 'check_in_time', 'check_out_time', 'rest', 'over_time', 'workdescription', 'work_comment', 'admin_domment', 'admin_description'
        ];
    }
}
