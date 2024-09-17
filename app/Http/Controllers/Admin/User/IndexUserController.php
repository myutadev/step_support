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
        if ($request->input('sortField')) {
            $sortField = $request->input('sortField');
            $sortOrder = $request->input('sortOrder');
        } else {
            //デフォルトは受給者番号の有効期限が近い人にする
            $sortField = 'beneficiary_number_expiration';
            $sortOrder = 'asc';
        }

        $userInfoArray = $this->userService->createUserAccountInfoObj();
        $userInfoArray = $this->userService->sortUserAccountInfoObj($userInfoArray, $sortField, $sortOrder);

        return view('admin.attendances.users', compact('userInfoArray', 'sortField', 'sortOrder'));
    }
}
