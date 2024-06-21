<?php

namespace App\Http\Controllers\Admin\Counselor;

use App\Http\Controllers\Controller;
use App\Services\CounselorService;
use Illuminate\Http\Request;

class IndexCounselorController extends Controller
{
    protected $counselorService;

    public function __construct(CounselorService $counselorService)
    {
        $this->counselorService = $counselorService;
    }

    public function __invoke()
    {
        $counselors = $this->counselorService->getAllCounselorsByCompanyId();
        return view('admin.attendances.counselors', compact('counselors'));
    }
}
