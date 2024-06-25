<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Services\UserWriteService;
use Illuminate\Http\Request;


class StoreUserController extends Controller
{

    protected $userCreateService;

    public function __construct(UserWriteService $userCreateService)
    {
        $this->userCreateService = $userCreateService;
    }

    public function __invoke(Request $request)
    {
        $this->userCreateService->storeUser($request);
        return redirect()->action(IndexUserController::class);
    }
}
