<?php

namespace App\Http\Controllers\Admin\Export;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use App\Utils\FileNameFormatter;

class ExportAttendanceController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $fileName = FileNameFormatter::generateAttendanceFileNameByNow();
        return Excel::download(new AttendanceExport($request->yearmonth), $fileName);
    }
}
