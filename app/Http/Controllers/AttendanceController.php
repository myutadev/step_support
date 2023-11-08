<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() // タイムカードの画面
    {
        return view('attendances.index');
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

        return redirect()->route('attendances.create');
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
