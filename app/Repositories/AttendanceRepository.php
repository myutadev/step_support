<?php

namespace App\Repositories;

use App\Models\Attendance;
use App\Models\AttendanceType;
use Illuminate\Database\Eloquent\Collection;

class AttendanceRepository
{
    private $attendance;

    public function __construct(Attendance $attendance)
    {
        $this->attendance = $attendance;
    }

    public function getAttendanceById($id)
    {
        return Attendance::with(['work_schedule', 'rests', 'overtimes', 'adminComments', 'user.userDetail', 'attendanceType'])->find($id);
    }

    public function create()
    {
        return new Attendance();
    }
}
