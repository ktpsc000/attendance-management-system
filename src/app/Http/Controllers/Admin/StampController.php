<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CorrectionRequest;


class StampController extends Controller
{
    public function index(Request $request){
        $tab = $request->query('tab', 'pending');
        $query = CorrectionRequest::with('user');

        if($tab === 'approved'){
            $query->where('status', CorrectionRequest::STATUS_APPROVED);
        }else{
            $query->where('status', CorrectionRequest::STATUS_PENDING);
        }

        $requests = $query->get();

        return view('admin.stamp_correction_request.index', compact('requests','tab'));
    }

    public function show($attendance_correct_request_id){
        $pendingRequest = CorrectionRequest::with('user','attendance')
            ->findOrFail($attendance_correct_request_id);

        $pendingBreaks = $pendingRequest->breakCorrectionRequests;

        return view('admin.stamp_correction_request.show', compact('pendingRequest','pendingBreaks'));
    }
}
