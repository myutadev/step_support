<?php

namespace App\Http\Controllers\Admin\Residence;

use App\Http\Controllers\Controller;
use App\Services\ResidenceService;
use Illuminate\Http\Request;

class DeleteResidenceController extends Controller
{
    protected $residenceService;

    public function __construct(ResidenceService $residenceService)
    {
        $this->residenceService = $residenceService;
    }
    public function __invoke($id)
    {
        $this->residenceService->deleteResidence($id);
        return redirect()->action(IndexResidenceController::class);
    }
}
