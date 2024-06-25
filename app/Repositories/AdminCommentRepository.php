<?php

namespace App\Repositories;

use App\Models\AdminComment;
use Illuminate\Database\Eloquent\Collection;

class AdminCommentRepository
{
    protected $adminComment;

    public function __construct(AdminComment $adminComment)
    {
        $this->adminComment = $adminComment;
    }

    public function getAdminCommentById($adminCommentId)
    {
        return AdminComment::with('attendance.work_schedule')->where('id', $adminCommentId)->first();
    }

    public function createNewAdminComment()
    {
        return new AdminComment();
    }

    public function getAdminCommentByAttendanceId($attendanceId)
    {
        return AdminComment::where('attendance_id', $attendanceId)->first();
    }
}
