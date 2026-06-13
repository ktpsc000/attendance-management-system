<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CorrectionRequest;

class StampController extends Controller
{
    public function index(){
        $requests = CorrectionRequest::where(
            'user_id', auth()->user())
        ->latest()
        ->get();

        return view('stamp_correction_request.index', compact('requests'));
    }
}
