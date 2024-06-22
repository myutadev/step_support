<?php

namespace App\Http\Controllers\Admin\Timecard;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class UpdateAttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function __invoke(Request $request, $id)
    {
        $this->attendanceService->updateAttendance($request, $id);
        return redirect()->route('admin.attendance.edit', $id);
    }
}
