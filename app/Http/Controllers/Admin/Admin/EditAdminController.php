<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminWriteService;
use Illuminate\Http\Request;

class EditAdminController extends Controller
{
    protected $adminWriteService;

    public function __construct(AdminWriteService $adminWriteService)
    {
        $this->adminWriteService = $adminWriteService;
    }

    public function __invoke($id)
    {
        $roles = $this->adminWriteService->getRoles();
        $admin = $this->adminWriteService->getEditAdminData($id);
        return view('admin.attendances.adminsedit', compact('admin', 'roles'));
    }
}
