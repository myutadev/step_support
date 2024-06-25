<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminWriteService;
use Illuminate\Http\Request;

class UpdateAdminController extends Controller
{
    protected $adminWriteService;

    public function __construct(AdminWriteService $adminWriteService)
    {
        $this->adminWriteService = $adminWriteService;
    }

    public function __invoke(Request $request, $id)
    {
        $this->adminWriteService->updateAdmin($request, $id);
        return redirect()->action(IndexAdminController::class);
    }
}
