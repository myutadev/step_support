<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Services\UserWriteService;

class CreateUserController extends Controller
{
    protected $userCreateService;

    public function __construct(UserWriteService $userCreateService)
    {
        $this->userCreateService = $userCreateService;
    }

    public function __invoke()
    {
        $userRegistrationData = $this->userCreateService->getUserResistrationData();
        return view('admin.attendances.userscreate', compact('userRegistrationData'));
    }
}
