<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\StampController;
use App\Http\Controllers\Admin\StampController as AdminStampController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::post('/register', [RegisteredUserController::class, 'store']);


Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');

Route::get('/admin/login', function () {
    session(['login_context' => 'admin']);
    return view('admin.auth.login');
})->middleware('guest');

Route::middleware(['auth', 'verified'])->group(function(){
Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance');
Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn']);
Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart']);
Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd']);
Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut']);

Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');

Route::get('/attendance/detail/{id}', [AttendanceController::class, 'detail'])->name('attendance.detail');
Route::post('/attendance/detail/{id}', [AttendanceController::class, 'store']);

Route::get('/stamp_correction_request/list', [StampController::class,'index'])->name('stamp_correction_request.list');

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function (){
    Route::get('/attendance/list',[AdminAttendanceController::class, 'index'])
    ->name('admin.attendance.list');

    Route::get('/attendance/{id}',[AdminAttendanceController::class, 'detail'])
    ->name('admin.attendance.detail');
    Route::post('/attendance/{id}',[AdminAttendanceController::class, 'store']);

    Route::get('/staff/list', [StaffController::class, 'index'])->name('admin.staff.list');

    Route::get('/attendance/staff/{id}', [AdminAttendanceController::class, 'list'])
    ->name('admin.attendance.staff');

    Route::post('/admin/attendance/staff/{id}/export', [AdminAttendanceController::class, 'export'])
    ->name('admin.attendance.export');

    Route::get('/stamp_correction_request/list', [AdminStampController::class, 'index'])->name('admin.stamp_correction_request.list');

    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminStampController::class, 'show'])->name('admin.stamp_correction_request.approve');
    });

});