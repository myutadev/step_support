<?php

namespace App\Http\Controllers\Admin\Residence;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResidenceRequest;
use App\Services\ResidenceService;
use Illuminate\Http\Request;

class UpdateResidenceController extends Controller
{
    protected $residenceService;

    public function __construct(ResidenceService $residenceService)
    {
        $this->residenceService = $residenceService;
    }
    public function __invoke(ResidenceRequest $request, $id)
    {
        $this->residenceService->updateResidence($request, $id);
        return redirect()->action(IndexResidenceController::class);
    }
}
