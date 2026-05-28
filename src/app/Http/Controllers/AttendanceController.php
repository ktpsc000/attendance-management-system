<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index() {
        $now = Carbon::now();
        return view('attendance.index' , compact('now'));
    }

}
