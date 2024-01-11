<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\Rest;
use App\Models\ScheduleType;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WorkSchedule;

use App\Models\UserDetail;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use DateInterval;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\FuncCall;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() // タイムカードの画面
    {
        // DBから当日の勤務情報を取り出して、配列に格納してViewに渡す。
        //[]種別、打刻日時、体温、作業内容、作業コメント
        // 1.当日のレコードの有無→退勤レコードの有無
        $userId = Auth::id();
        $today = Carbon::today();
        $workSchedule = WorkSchedule::where('date', $today)->first();
        $attendance = Attendance::where('user_id', $userId)->where('work_schedule_id', $workSchedule->id)->first();
        if ($attendance) {
            $rests = Rest::where('attendance_id', $attendance->id)->get();
            $overtimes = Overtime::where('attendance_id', $attendance->id)->get();
        }

        $attendancesArray = [];

        if ($attendance) {
            $checkIn = [
                'attendance_id' => $attendance->id,
                'type' => '出勤',
                'dateTime' => $workSchedule->date . ' ' .  Carbon::parse($attendance->check_in_time)->format('H:i'),
                'body_temp' => $attendance->body_temp,
                'work_description' => "",
                'work_comment' => "",
                "edit_button" => "",
            ];
            array_unshift($attendancesArray, $checkIn);

            if ($rests) {
                foreach ($rests as $rest) {
                    $restStart  = [
                        'attendance_id' => $attendance->id,
                        'type' => '休憩開始',
                        'dateTime' => $workSchedule->date . ' ' .  Carbon::parse($rest->start_time)->format('H:i'),
                        'body_temp' => "",
                        'work_description' => "",
                        'work_comment' => "",
                        "edit_button" => "",
                    ];

                    array_unshift($attendancesArray, $restStart);

                    if (!$rest->end_time == "") {
                        $restEnd  = [
                            'attendance_id' => $attendance->id,
                            'type' => '休憩終了',
                            'dateTime' => $workSchedule->date . ' ' .  Carbon::parse($rest->end_time)->format('H:i'),
                            'body_temp' => "",
                            'work_description' => "",
                            'work_comment' => "",
                            "edit_button" => "",
                        ];
                        array_unshift($attendancesArray, $restEnd);
                    }
                }
            }


            if (!$attendance->check_out_time == "") {
                $checkOut = [
                    'attendance_id' => $attendance->id,
                    'type' => '退勤',
                    'dateTime' => $workSchedule->date . ' ' .  Carbon::parse($attendance->check_out_time)->format('H:i'),
                    'body_temp' => "",
                    'work_description' => $attendance->work_description,
                    'work_comment' => $attendance->work_comment,
                    "edit_button" => 1,

                ];
                array_unshift($attendancesArray, $checkOut);
            }

            if ($overtimes) {
                foreach ($overtimes as $overtime) {
                    $overtimeStart  = [
                        'attendance_id' => $attendance->id,
                        'type' => '残業開始',
                        'dateTime' => $workSchedule->date . ' ' .  Carbon::parse($overtime->start_time)->format('H:i'),
                        'body_temp' => "",
                        'work_description' => "",
                        'work_comment' => "",
                        "edit_button" => "",
                    ];

                    array_unshift($attendancesArray, $overtimeStart);

                    if (!$overtime->end_time == "") {
                        $overtimeEnd  = [
                            'attendance_id' => $attendance->id,
                            'type' => '残業終了',
                            'dateTime' => $workSchedule->date . ' ' .  Carbon::parse($overtime->end_time)->format('H:i'),
                            'body_temp' => "",
                            'work_description' => "",
                            'work_comment' => "",
                            "edit_button" => "",
                        ];
                        array_unshift($attendancesArray, $overtimeEnd);
                    }
                }
            }



            // dd($attendancesArray);
        }
        return view('attendances.index', compact('attendancesArray'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() // 打刻画面
    {
        return view('attendances.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    }


    /**
     *   利用者さん出退勤登録用Postメソッド

     */
    public function checkin(Request $request)
    {
        $attendance = new Attendance;
        //requestからuser()メソッドを使うのはミドルウェアを通過したリクエストで使えるようになる。
        $attendance->user_id = $request->user()->id;
        //company_idをuser_detailsから取り出す
        $user = $request->user();
        $userDetail = $user->userDetail;

        $attendance->company_id = $userDetail->company_id;
        $attendance->attendance_type_id = 1;
        $attendance->check_in_time = Carbon::now()->toTimeString();

        //work_schedulesからdateidを取得
        $today = Carbon::today();
        $workSchedule = WorkSchedule::where('date', $today)->first();
        $attendance->work_schedule_id = $workSchedule->id;

        $attendance->body_temp = $request->body_temp;
        $attendance->save();

        return redirect()->route('attendances.index')->with(
            'requested',
            '打刻が完了しました! 
        今日も一日がんばりましょう!'
        );
    }


    public function checkout(Request $request)
    {
        $userId = $request->user()->id;
        $today = Carbon::today();
        $workSchedule = WorkSchedule::where('date', $today)->first();
        $attendance = Attendance::where('user_id', $userId)->where('work_schedule_id', $workSchedule->id)->first();
        $attendance->check_out_time = Carbon::now()->toTimeString();
        $attendance->work_description = $request->work_description;
        $attendance->work_comment = $request->work_comment;
        $attendance->update();

        return redirect()->route('attendances.index')->with(
            'requested',
            '打刻が完了しました!
        今日も一日お疲れ様でした!'
        );
    }

    public function restStart()
    {
        $rest = new Rest;
        $userId = Auth::id();
        $today = Carbon::today();
        //work_schedulesからdateidを取得
        $workSchedule = WorkSchedule::where('date', $today)->first();
        $attendance = Attendance::where('user_id', $userId)->where('work_schedule_id', $workSchedule->id)->first();
        $attendance_id = $attendance->id;

        $rest->attendance_id = $attendance_id;
        $rest->start_time = Carbon::now()->toTimeString();
        $rest->save();

        return redirect()->route('attendances.index')->with('requested', "休憩開始しました");
    }

    public function restEnd()
    {
        $userId = Auth::id();
        $today = Carbon::today();
        $workSchedule = WorkSchedule::where('date', $today)->first();
        $attendance = Attendance::where('user_id', $userId)->where('work_schedule_id', $workSchedule->id)->first();
        $attendanceId = $attendance->id;
        $rest = Rest::where('end_time', null)->where('attendance_id', $attendanceId)->first();
        $rest->end_time = Carbon::now()->toTimeString();
        $rest->update();

        return redirect()->route('attendances.index')->with('requested', "休憩終了しました");
    }

    public function overtimeStart()
    {
        $overtime = new Overtime();
        $userId = Auth::id();
        $today = Carbon::today();
        $workSchedule = WorkSchedule::where('date', $today)->first();
        $attendance = Attendance::where('user_id', $userId)->where('work_schedule_id', $workSchedule->id)->first();
        $attendance_id = $attendance->id;
        $overtime->attendance_id = $attendance_id;
        $overtime->start_time = Carbon::now()->toTimeString();
        $overtime->save();

        return redirect()->route('attendances.index')->with('requested', "残業開始しました");
    }

    public function overtimeEnd()
    {
        $userId = Auth::id();
        $today = Carbon::today();
        $workSchedule = WorkSchedule::where('date', $today)->first();
        $attendance = Attendance::where('user_id', $userId)->where('work_schedule_id', $workSchedule->id)->first();
        $attendanceId = $attendance->id;
        $overtime = Overtime::where('end_time', null)->where('attendance_id', $attendanceId)->first();
        $overtime->end_time = Carbon::now()->toTimeString();
        $overtime->update();

        return redirect()->route('attendances.index')->with('requested', "残業終了しました");
    }

    public function timecard($yearmonth = null)
    {
        $user = Auth::user();

        if (is_null($yearmonth)) {
            $today = Carbon::today();
            $year = $today->year;
            $month = $today->month;
        } else {
            $yearMonthArr = explode("-", $yearmonth);
            $year = $yearMonthArr[0];
            $month = $yearMonthArr[1];
        }


        $monthlyAttendanceData = [];

        $thisMonthWorkSchedules = WorkSchedule::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'asc')
            ->get();

        foreach ($thisMonthWorkSchedules as $WorkSchedule) {
            $curScheduleType = ScheduleType::where('id', $WorkSchedule->schedule_type_id)->first();
            $curAttendance = Attendance::where('user_id', $user->id)->where('work_schedule_id', $WorkSchedule->id)->first();
            //curAttenddanceがNull→まだ出勤されてない場合
            if (!$curAttendance) {
                $curAttendObj = [
                    'date' => $WorkSchedule->date,
                    'scheduleType' => $curScheduleType->name,
                    'bodyTemp' => "",
                    'checkin' => "",
                    'checkout' => "",
                    'rest' => "",
                    'overtime' => "",
                    'duration' => "",
                    'workDescription' => "",
                    'workComment' => "",
                ];
                array_push($monthlyAttendanceData, $curAttendObj);
            } else {


                $curRests = Rest::where('attendance_id', $curAttendance->id)->get();
                //休憩は複数回入る可能性あり。
                $restTimes = [];
                foreach ($curRests as $rest) {
                    $restTimes[] = Carbon::parse($rest->start_time)->format('H:i') . '-' . Carbon::parse($rest->end_time)->format('H:i');
                }
                $restTimeString = implode("<br>", $restTimes);

                $curOvertime = Overtime::where('attendance_id', $curAttendance->id)->first();

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

                $curAttendObj = [
                    'date' => $WorkSchedule->date,
                    'scheduleType' => $curScheduleType->name,
                    'bodyTemp' => $curAttendance->body_temp,
                    'checkin' => Carbon::parse($curAttendance->check_in_time)->format('H:i'),
                    'checkout' => $curAttendance->check_out_time == null ? "" : Carbon::parse($curAttendance->check_out_time)->format('H:i'),
                    'rest' => $restTimeString,
                    'overtime' => $curOvertime == null ? "" : Carbon::parse($curOvertime->start_time)->format('H:i') . '-' . Carbon::parse($curOvertime->end_time)->format('H:i'),
                    'duration' => $workDurationInterval->format('%H:%I:%S'),
                    'workDescription' => $curAttendance->work_description,
                    'workComment' => $curAttendance->work_comment,
                ];


                array_push($monthlyAttendanceData, $curAttendObj);
            }
        }
        return view('attendances.timecard', compact('monthlyAttendanceData', 'year', 'month'));
    }



    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $attendance->work_description = $request->work_description;
        $attendance->work_comment = $request->work_comment;
        $attendance->update();
        return $this->index();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
