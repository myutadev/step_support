<?php

namespace App\Http\Controllers\Admin\Counselor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateCounselorController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return view('admin.attendances.counselorcreate');
    }
}
