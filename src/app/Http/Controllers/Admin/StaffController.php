<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class StaffController extends Controller
{
    public function index(){
        $userList = User::where('role', User::ROLE_USER)->get();

        foreach($userList as $user){

            $users[] = [
                'user' => $user,
                'email' => $user->email,
            ];
        }

        return view('admin.staff.list', compact('users'));
    }

}
