<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'clock_in_at',
        'clock_out_at',
        'remarks',
    ];

    protected $casts = [
        'work_date' => 'date',
        'clock_in_at' => 'datetime',
        'clock_out_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(AttendanceBreak::class);
    }

    public function correctionRequests()
    {
        return $this->hasMany(CorrectionRequest::class);
    }

    public function getBreakMinutes(){
        $totalMinutes = 0;

        foreach ($this->breaks as $break){
            if($break->break_start_at && $break->break_end_at){
                $totalMinutes += $break->break_start_at
                ->diffInMinutes($break->break_end_at);
            }
        }

        return $totalMinutes;
    }

    public function getFormattedBreakTime()
    {
        $minutes = $this->getBreakMinutes();

        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;

        return sprintf('%d:%02d', $hours, $minutes);
    }

    public function getWorkingMinutes()
    {
        if (!$this->clock_in_at || !$this->clock_out_at) {
            return 0;
        }

        $workMinutes = $this->clock_in_at
            ->diffInMinutes($this->clock_out_at);

        return $workMinutes - $this->getBreakMinutes();
    }

    public function getFormattedWorkingTime()
    {
        $minutes = $this->getWorkingMinutes();

        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;

        return sprintf('%d:%02d', $hours, $minutes);
    }



}
