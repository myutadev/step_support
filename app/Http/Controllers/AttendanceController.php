<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\Rest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDetail;
use Carbon\Carbon;
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
        $attendance = Attendance::where('user_id', $userId)->whereDate('date', $today)->first();
        if ($attendance) {
            $rests = Rest::where('attendance_id', $attendance->id)->get();
            $overtimes = Overtime::where('attendance_id', $attendance->id)->get();
        }

        $attendancesArray = [];

        if ($attendance) {
            $checkIn = [
                'type' => '出勤',
                'dateTime' => $attendance->date . ' ' .  $attendance->check_in_time,
                'body_temp' => $attendance->body_temp,
                'work_description' => "",
                'work_comment' => "",
                "edit_button" => "",
            ];
            array_unshift($attendancesArray, $checkIn);

            if ($rests) {
                foreach ($rests as $rest) {
                    $restStart  = [
                        'type' => '休憩開始',
                        'dateTime' => $attendance->date . ' ' .  $rest->start_time,
                        'body_temp' => "",
                        'work_description' => "",
                        'work_comment' => "",
                        "edit_button" => "",
                    ];

                    array_unshift($attendancesArray, $restStart);

                    if (!$rest->end_time == "") {
                        $restEnd  = [
                            'type' => '休憩終了',
                            'dateTime' => $attendance->date . ' ' .  $rest->end_time,
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
                    'type' => '退勤',
                    'dateTime' => $attendance->date . ' ' .  $attendance->check_out_time,
                    'body_temp' => "",
                    'work_description' => $attendance->work_description,
                    'work_comment' => $attendance->work_comment,
                    "edit_button" => "",

                ];
                array_unshift($attendancesArray, $checkOut);
            }

            if ($overtimes) {
                foreach ($overtimes as $overtime) {
                    $overtimeStart  = [
                        'type' => '残業開始',
                        'dateTime' => $attendance->date . ' ' .  $overtime->start_time,
                        'body_temp' => "",
                        'work_description' => "",
                        'work_comment' => "",
                        "edit_button" => "",
                    ];

                    array_unshift($attendancesArray, $overtimeStart);

                    if (!$overtime->end_time == "") {
                        $overtimeEnd  = [
                            'type' => '残業終了',
                            'dateTime' => $attendance->date . ' ' .  $overtime->end_time,
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
        $attendance->date = Carbon::now()->toDateString();
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
        $attendance = Attendance::where('user_id', $userId)->whereDate('date', $today)->first();
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

        $attendance = Attendance::where('user_id', $userId)->whereDate('date', $today)->first();
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
        $attendance =  Attendance::where('user_id', $userId)->whereDate('date', $today)->first();
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
        $attendance = Attendance::where('user_id', $userId)->whereDate('date', $today)->first();
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
        $attendance =  Attendance::where('user_id', $userId)->whereDate('date', $today)->first();
        $attendanceId = $attendance->id;
        $overtime = Overtime::where('end_time', null)->where('attendance_id', $attendanceId)->first();
        $overtime->end_time = Carbon::now()->toTimeString();
        $overtime->update();

        return redirect()->route('attendances.index')->with('requested', "残業終了しました");
    }







    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
