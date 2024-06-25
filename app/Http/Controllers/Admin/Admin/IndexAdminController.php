<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\AdminRepository;
use App\Services\AdminWriteService;
use Illuminate\Http\Request;

class IndexAdminController extends Controller
{
    protected $adminWriteService;

    public function __construct(AdminWriteService $adminWriteService)
    {
        $this->adminWriteService = $adminWriteService;
    }

    public function __invoke()
    {
        $adminInfoArray = $this->adminWriteService->getAdminIndexData();
        return view('admin.attendances.admins', compact('adminInfoArray'));
    }
}
