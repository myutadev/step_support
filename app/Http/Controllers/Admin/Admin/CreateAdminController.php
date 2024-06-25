<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminWriteService;

class CreateAdminController extends Controller
{
    protected $adminWriteService;

    public function __construct(AdminWriteService $adminWriteService)
    {
        $this->adminWriteService = $adminWriteService;
    }

    public function __invoke()
    {
        $roles = $this->adminWriteService->getRoles();
        return view('admin.attendances.adminscreate', compact('roles'));
    }
}
