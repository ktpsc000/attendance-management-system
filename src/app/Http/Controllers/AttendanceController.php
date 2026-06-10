<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceController extends Controller
{
    public function create() {
        $user = auth()->user();
        $todayAttendance = Attendance::where('user_id', $user->id)
        ->where('work_date', today())
        ->first();

        return view('attendance.index' , compact('user','todayAttendance'));
    }

    public function clockIn(){
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

    $attendances = Attendance::where('user_id',$user->id)
        ->whereYear('work_date', $year)
        ->whereMonth('work_date', $month)
        ->get()
        ->keyBy(function($attendance){
            return $attendance->work_date->format('Y-m-d');
        });

        $days = [];

        foreach(
            CarbonPeriod::create(
                $currentMonth->copy()->startOfMonth(),
                $currentMonth->copy()->endOfMonth()
            ) as $day
        ){
            $date = $day->format('Y-m-d');

            $days[] = [
                'day' => $day,
                'attendance' => $attendances[$date] ?? null,
            ];
        }

        return view('attendance.list', compact('user','days','currentMonth'));
    }

}
