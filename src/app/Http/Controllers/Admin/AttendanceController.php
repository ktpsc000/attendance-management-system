<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\CorrectionRequest;
use App\Http\Requests\AttendanceCorrectionRequest;

class AttendanceController extends Controller
{
    public function index(Request $request){

        $currentDay = Carbon::parse(
            $request->input('date', now()->format('Y-m-d'),)
        );

        $userList = User::where('role', User::ROLE_USER)->get();
        $attendances = Attendance::whereDate('work_date', $currentDay)->get()->keyBy('user_id');
        $users = [];

        foreach($userList as $user){

            $attendance = $attendances->get($user->id);

            if (!$attendance) {
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'work_date' => $currentDay->format('Y-m-d'),
                ]);
            }

            $users[] = [
                'user' => $user,
                'attendance' => $attendance,
            ];
        }

        return view('admin.attendance.list', compact('users','currentDay'));
    }

    public function detail($id){
        $attendance = Attendance::with('user')
            ->findOrFail($id);

        $pendingRequest = $attendance->correctionRequests()
            ->where('status', CorrectionRequest::STATUS_PENDING)
            ->latest()
            ->first();

        $pendingBreaks = $pendingRequest ? $pendingRequest->breakCorrectionRequests : $attendance->breaks;

        return view('admin.attendance.detail', compact('attendance','pendingRequest','pendingBreaks'));
    }

    public function store(AttendanceCorrectionRequest $request, $id){
        $attendance = Attendance::with('breaks')
            ->findOrFail($id);

        $attendance->update([
                'clock_in_at' => $request->clock_in_at,
                'clock_out_at' => $request->clock_out_at,
                'remarks' => $request->remarks,
            ]);

        if($request->filled('break_ids')){
            foreach ($request->break_ids as $index => $breakId){

                $break = $attendance->breaks->firstWhere('id',$breakId);

                if (!$break){
                    continue;
                }

                $break->update([
                    'break_start_at' => $request->break_start_at[$index],
                'break_end_at' => $request->break_end_at[$index],
                ]);
            }
        }

        if ($request->new_break_start_at && $request->new_break_end_at){
            $attendance->breaks()->create([
                'break_start_at' => $request->new_break_start_at,
                'break_end_at' => $request->new_break_end_at,
            ]);
        }

        return redirect('/admin/attendance/list');
    }

    public function list(Request $request, $id){

        $user = User::findOrFail($id);
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $currentMonth = Carbon::create($year, $month);
        $days = $this->getAttendanceList($user, $currentMonth);

        return view('admin.attendance.staff_list', compact('user','days','currentMonth'));
    }


    public function export(Request $request, $id){
        $user = User::findOrFail($id);
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $currentMonth = Carbon::create($year, $month);
        $days = $this->getAttendanceList($user, $currentMonth);

        return response()->streamDownload(function () use ($days) {

            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                '日付',
                '出勤',
                '退勤',
                '休憩',
                '合計',
            ]);

            foreach ($days as $day) {
                fputcsv($handle, [
                    $day['day']->format('Y/m/d'),
                    optional($day['attendance']->clock_in_at)->format('H:i'),
                    optional($day['attendance']->clock_out_at)->format('H:i'),
                    optional($day['attendance'])->getFormattedBreakTime(),
                    optional($day['attendance'])->getFormattedWorkingTime(),
                ]);
            }

            fclose($handle);

        }, "{$user->name}_{$currentMonth->format('Ym')}.csv");
    }

    private function getAttendanceList(User $user, Carbon $currentMonth){
        $days = [];

        foreach (CarbonPeriod::create(
                $currentMonth->copy()->startOfMonth(),
                $currentMonth->copy()->endOfMonth()
            ) as $day){

            $attendance = Attendance::firstOrCreate([
                'user_id' => $user->id,
                'work_date' => $day->format('Y-m-d'),
            ]);

            $days[] = [
                'day' => $day,
                'attendance' => $attendance,
            ];
        }

        return $days;
    }

}
