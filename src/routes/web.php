<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampController;
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

});