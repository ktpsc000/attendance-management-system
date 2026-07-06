<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use App\Models\CorrectionRequest;
use App\Models\BreakCorrectionRequest;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Http\Requests\AttendanceCorrectionRequest;

class AttendanceController extends Controller
{
    public function create() {
        $user = auth()->user();
        $todayAttendance = Attendance::firstOrCreate([
            'user_id' => $user->id,
            'work_date' => today(),
        ]);

        return view('attendance.index' , compact('user','todayAttendance'));
    }

    public function clockIn(){
        $user = auth()->user();
        $attendance = Attendance::firstOrCreate([
            'user_id' => $user->id,
            'work_date' => today(),
        ]);

        $attendance->update([
            'clock_in_at' => now(),
        ]);

        $user->update([
            'status' => User::STATUS_WORKING,
        ]);
        return back();
    }

    public function breakStart(){
        $user = auth()->user();
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('work_date', today())
            ->firstOrFail();

        $attendance->breaks()->create([
            'break_start_at' => now(),
        ]);

        $user->update([
            'status' => User::STATUS_BREAK,
        ]);
        return back();
    }

    public function breakEnd(){
        $user = auth()->user();
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('work_date', today())
            ->firstOrFail();
        $break = $attendance->breaks()
            ->whereNull('break_end_at')
            ->latest()
            ->firstOrFail();

        $break->update([
            'break_end_at' => now(),
        ]);

        $user->update([
            'status' => User::STATUS_WORKING,
        ]);
        return back();
    }

    public function clockOut(){
        $user = auth()->user();
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('work_date', today())
            ->firstOrFail();

        $attendance->update([
            'clock_out_at' => now(),
        ]);

        $user->update([
            'status' => User::STATUS_OFF_DUTY,
        ]);
        return back();
    }

    public function list(Request $request){
        $user = auth()->user();
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $currentMonth = Carbon::create($year, $month);
        $days = [];

        foreach(
            CarbonPeriod::create(
                $currentMonth->copy()->startOfMonth(),
                $currentMonth->copy()->endOfMonth()
            ) as $day
        ){
            $attendance = Attendance::firstOrCreate([
                'user_id' => $user->id,
                'work_date' => $day->format('Y-m-d'),
            ]);

            $days[] = [
                'day' => $day,
                'attendance' => $attendance,
            ];
        }

        return view('attendance.list', compact('user','days','currentMonth'));
    }

    public function detail($id){
        $attendance = Attendance::with('user')
            ->findOrFail($id);

        $pendingRequest = $attendance->correctionRequests()
        ->where('status', CorrectionRequest::STATUS_PENDING)
            ->latest()
            ->first();

        $pendingBreaks = $pendingRequest ? $pendingRequest->breakCorrectionRequests : $attendance->breaks;

        return view('attendance.detail', compact('attendance','pendingRequest','pendingBreaks'));
    }


    public function store(AttendanceCorrectionRequest $request, $id){
        $attendance = Attendance::findOrFail($id);

        $correctionRequest = CorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => auth()->id(),

            'request_clock_in_at' => $request->clock_in_at,
            'request_clock_out_at' => $request->clock_out_at,

            'remarks' => $request->remarks,

            'status' => CorrectionRequest::STATUS_PENDING,
        ]);

        foreach ($request->break_start_at as $index => $start){
            $end = $request->break_end_at[$index];

            if (!$start || !$end) {
                continue;
            }

            BreakCorrectionRequest::create([
                'correction_request_id' => $correctionRequest->id,
                'request_break_start_at' => $start,
                'request_break_end_at' => $end,
            ]);
        }

        return redirect('/attendance/list');
    }

}
