<?php

namespace App\Http\Controllers\Admin\Counselor;

use App\Http\Controllers\Controller;
use App\Services\CounselorService;

class EditCounselorController extends Controller
{
    protected $counselorService;

    public function __construct(CounselorService $counselorService)
    {
        $this->counselorService = $counselorService;
    }


    public function __invoke($id)
    {
        $counselor = $this->counselorService->getCounselorById($id);
        return view('admin.attendances.counselorsedit', compact('counselor'));
    }
}
