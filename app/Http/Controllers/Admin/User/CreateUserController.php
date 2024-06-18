<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Services\UserWriteService;

class CreateUserController extends Controller
{
    protected $userWriteService;

    public function __construct(UserWriteService $userWriteService)
    {
        $this->userWriteService = $userWriteService;
    }

    public function __invoke()
    {
        $userRegistrationData = $this->userWriteService->getUserResistrationData();
        return view('admin.attendances.userscreate', compact('userRegistrationData'));
    }
}
