<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //  一般ユーザー：0  管理者：1
    public const ROLE_USER = 0;
    public const ROLE_ADMIN = 1;

    //  勤務外:0  勤務中:1  休憩中:2
    public const STATUS_OFF_DUTY = 0;
    public const STATUS_WORKING = 1;
    public const STATUS_BREAK = 2;


    public const STATUS_LABELS = [
        self::STATUS_OFF_DUTY => '勤務外',
        self::STATUS_WORKING => '出勤中',
        self::STATUS_BREAK => '休憩中',
    ];

    public function isOffDuty()
    {
        $todayAttendance = $this->attendances()
            ->whereDate('work_date', today())
            ->first();

        return $todayAttendance
            && $todayAttendance->clock_in_at === null;
    }

    public function isWorking(){
        return $this->status === self::STATUS_WORKING;
    }

    public function isBreak(){
        return $this->status === self::STATUS_BREAK;
    }

    public function isFinished(){
        $todayAttendance = $this->attendances()
            ->whereDate('work_date',today())
            ->first();

        return $todayAttendance && $todayAttendance->clock_out_at !== null;
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function correctionRequests()
    {
        return $this->hasMany(CorrectionRequest::class);
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function getStatusLabel()
    {
        $todayAttendance = $this->attendances()
            ->whereDate('work_date',today())
            ->first();

        if($todayAttendance && $todayAttendance->clock_out_at){
            return '退勤済';
        }

        return self::STATUS_LABELS[$this->status];
    }

}
