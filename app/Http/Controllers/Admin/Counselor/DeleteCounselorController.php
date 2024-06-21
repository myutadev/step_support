<?php

namespace App\Http\Controllers\Admin\Counselor;

use App\Http\Controllers\Controller;
use App\Services\CounselorService;

class DeleteCounselorController extends Controller
{
    protected $counselorService;

    public function __construct(CounselorService $counselorService)
    {
        $this->counselorService = $counselorService;
    }

    public function __invoke($id)
    {
        $this->counselorService->deleteCounselor($id);
        return redirect()->action(IndexCounselorController::class);
    }
}
