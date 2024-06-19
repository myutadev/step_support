<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Services\UserWriteService;

class EditUserController extends Controller
{
    protected $userWriteService;

    public function __construct(UserWriteService $userWriteService)
    {
        $this->userWriteService = $userWriteService;
    }

    public function __invoke($id)
    {
        $userRegistrationData = $this->userWriteService->getUserResistrationData();
        $user = $this->userWriteService->getUserWithDetailsByUserId($id);

        return view('admin.attendances.usersedit', compact('userRegistrationData', 'user'));
    }
}
