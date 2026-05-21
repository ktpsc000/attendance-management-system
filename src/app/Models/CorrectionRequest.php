<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'request_clock_in_at',
        'request_clock_out_at',
        'remarks',
        'status',
    ];

    protected $casts = [
        'request_clock_in_at' => 'datetime',
        'request_clock_out_at' => 'datetime',
    ];

    public function attendances()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function users()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function breakCorrectionRequests()
    {
        return $this->hasMany(BreakCorrectionRequest::class);
    }
}

