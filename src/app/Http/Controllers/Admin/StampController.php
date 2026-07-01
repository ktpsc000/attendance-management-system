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

    public function approve($attendance_correct_request_id){
        $pendingRequest = CorrectionRequest::with('attendance.breaks','breakCorrectionRequests')
            ->findOrFail($attendance_correct_request_id);

        $attendance = $pendingRequest->attendance;
        $attendanceBreaks = $attendance->breaks;

        $attendance->update([
            'clock_in_at'  => $pendingRequest->request_clock_in_at,
            'clock_out_at' => $pendingRequest->request_clock_out_at,
            'remarks'      => $pendingRequest->remarks,
        ]);

        foreach ($pendingRequest->breakCorrectionRequests as $index => $breakRequest) {
            $break = $attendanceBreaks->get($index);

            if ($break) {
                $break->update([
                    'break_start_at' => $breakRequest->request_break_start_at,
                    'break_end_at'   => $breakRequest->request_break_end_at,
                ]);
            }else {
                $attendance->breaks()->create([
                    'break_start_at' => $breakRequest->request_break_start_at,
                    'break_end_at'   => $breakRequest->request_break_end_at,
                ]);
            }

        }
        $pendingRequest->update([
            'status' => CorrectionRequest::STATUS_APPROVED,
        ]);

        return redirect()->route('admin.stamp_correction_request.show', $pendingRequest->id);
    }
}
