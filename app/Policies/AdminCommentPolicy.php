<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\AdminComment;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCommentPolicy
{
    /**
     * Create a new policy instance.
     */


    public function __construct()
    {
        //
    }

    public function update(Admin $admin, AdminComment $adminComment)
    {
        return $admin->id === $adminComment->admin_id || $adminComment->admin_id == null;
    }
}
