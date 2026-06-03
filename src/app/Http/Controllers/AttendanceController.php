<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;

class AttendanceController extends Controller
{
    public function index() {
        $user = auth()->user();
        $todayAttendance = Attendance::where('user_id', $user->id)
        ->where('work_date', today())
        ->first();

        return view('attendance.index' , compact('user','todayAttendance'));
    }

    public function clockIn()
    {
        $user = auth()->user();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in_at' => now(),
        ]);

        $user->update([
            'status' => User::STATUS_WORKING,
        ]);

        return back();
    }

    public function breakStart()
    {
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

    public function breakEnd()
    {
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

    public function clockOut()
    {
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



}
