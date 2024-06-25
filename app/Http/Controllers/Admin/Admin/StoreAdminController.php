<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminWriteService;
use Illuminate\Http\Request;

class StoreAdminController extends Controller
{
    protected $adminWriteService;

    public function __construct(AdminWriteService $adminWriteService)
    {
        $this->adminWriteService = $adminWriteService;
    }
    public function __invoke(Request $request)
    {
        $this->adminWriteService->storeAdmin($request);
        return redirect()->route('admin.admins');
    }
}
