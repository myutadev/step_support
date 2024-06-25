<?php

namespace App\Http\Controllers\Admin\Timecard;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class EditAttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function __invoke($id)
    {
        // 体温･出勤 退勤 休憩 残業有無 休憩 残業  勤務時間 作業内容 作業コメント 利用者名 利用者番号 日付 勤務カテゴリ
        // 編集可能: 出退勤 休憩 残業 体温
        // 他は表示のみ
        $attendanceTypes = $this->attendanceService->generateEditAttendaceData();

        $attendance = $this->attendanceService->getAttendanceById($id);

        return view('admin.attendances.admintimecardedit', compact('attendance', 'attendanceTypes'));
    }
}
