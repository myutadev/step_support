<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\WorkScheduleService;
use Illuminate\Http\Request;

class IndexUserController extends Controller
{
    protected $workScheduleService;
    protected $userService;

    public function __construct(

        WorkScheduleService $workScheduleService,
        UserService $userService
    ) {
        $this->workScheduleService = $workScheduleService;
        $this->userService = $userService;
    }

    public function __invoke(Request $request)
    {
        $userInfoArray = $this->userService->createUserAccountInfoObj();
        return view('admin.attendances.users', compact('userInfoArray'));
    }
}
