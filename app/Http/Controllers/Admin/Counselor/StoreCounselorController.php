<?php

namespace App\Http\Controllers\Admin\Counselor;

use App\Http\Controllers\Controller;
use App\Http\Requests\CounselorRequest;
use App\Services\CounselorService;
use Illuminate\Http\Request;

class StoreCounselorController extends Controller
{
    protected $counselorService;

    public function __construct(CounselorService $counselorService)
    {
        $this->counselorService = $counselorService;
    }

    public function __invoke(CounselorRequest $request)
    {
        $this->counselorService->storeCounselor($request);
        return redirect()->action(IndexCounselorController::class);
    }
}
