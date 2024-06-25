<?php

namespace App\Http\Controllers\Admin\Counselor;

use App\Http\Controllers\Controller;
use App\Http\Requests\CounselorRequest;
use App\Services\CounselorService;
use Illuminate\Http\Request;

class UpdateCounselorController extends Controller
{
    protected $counselorService;

    public function __construct(CounselorService $counselorService)
    {
        $this->counselorService = $counselorService;
    }

    public function __invoke(CounselorRequest $request, $id)
    {
        $this->counselorService->updateCounselor($request, $id);
        return redirect()->action(IndexCounselorController::class);
    }
}
