<?php

namespace App\Http\Controllers\Admin\Export;

use App\Http\Controllers\Controller;
use App\Services\MonthUserSelectorService;
use Illuminate\Http\Request;

class ShowExportPageController extends Controller
{
    protected $monthUserSelectorService;

    public function __construct(
        MonthUserSelectorService $monthUserSelectorService,
    ) {
        $this->monthUserSelectorService = $monthUserSelectorService;
    }

    public function __invoke($yearmonth = null)
    {
        $selectedYearMonth = $this->monthUserSelectorService->getSelectedYearMonth($yearmonth);

        return view('admin.attendances.exportshow', compact(['selectedYearMonth']));
    }
}
