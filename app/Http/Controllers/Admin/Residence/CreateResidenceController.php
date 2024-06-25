<?php

namespace App\Http\Controllers\Admin\Residence;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateResidenceController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return view('admin.attendances.residencecreate');
    }
}
