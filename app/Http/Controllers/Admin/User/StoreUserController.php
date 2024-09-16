<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Services\UserWriteService;


class StoreUserController extends Controller
{

    protected $userCreateService;

    public function __construct(UserWriteService $userCreateService)
    {
        $this->userCreateService = $userCreateService;
    }

    public function __invoke(UserRequest $request)
    {
        $this->userCreateService->storeUser($request);
        return redirect()->action(IndexUserController::class);
    }
}
