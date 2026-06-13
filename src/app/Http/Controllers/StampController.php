<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CorrectionRequest;

class StampController extends Controller
{
    public function index(){
        $user = auth()->user();
        $requests = CorrectionRequest::with('user')
        ->where('user_id', $user->id)
        ->latest()
        ->get();

        return view('stamp_correction_request.index', compact('requests'));
    }
}
