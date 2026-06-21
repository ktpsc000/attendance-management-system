<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request){

        $currentDay = Carbon::parse(
            $request->input('date', now()->format('Y-m-d'),)
        );

        $userList = User::where('role', User::ROLE_USER)->get();
        $attendances = Attendance::where('work_date', $currentDay)->get()->keyBy('user_id');
        $users = [];

        foreach($userList as $user){
            $users[] = [
                'user' => $user,
                'attendance' => $attendances[$user->id],
            ];
        }

        return view('admin.attendance.list', compact('users','currentDay'));
    }
}
