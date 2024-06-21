<?php

namespace App\Http\Controllers\Admin\Residence;

use App\Http\Controllers\Controller;
use App\Services\ResidenceService;

class EditResidenceController extends Controller
{
    protected $residenceService;

    public function __construct(ResidenceService $residenceService)
    {
        $this->residenceService = $residenceService;
    }
    public function __invoke($id)
    {
        $residence = $this->residenceService->getResidenceById($id);
        return view('admin.attendances.residencesedit', compact('residence'));
    }
}
