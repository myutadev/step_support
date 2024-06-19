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
}