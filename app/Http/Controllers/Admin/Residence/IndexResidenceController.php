<?php

namespace App\Http\Controllers\Admin\Residence;

use App\Http\Controllers\Controller;
use App\Services\ResidenceService;

class IndexResidenceController extends Controller
{
    protected $residenceService;

    public function __construct(ResidenceService $residenceService)
    {
        $this->residenceService = $residenceService;
    }
    public function __invoke()
    {
        $residences = $this->residenceService->getAllResidencesByCompanyId();
        return view('admin.attendances.residences', compact('residences'));
    }
}
