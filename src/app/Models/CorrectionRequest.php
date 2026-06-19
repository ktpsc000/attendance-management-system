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

    // 承認待ち:1  承認済み:2
    public const STATUS_PENDING = 1;
    public const STATUS_APPROVED = 2;

    public const STATUS_LABELS = [
        self::STATUS_PENDING => '承認待ち',
        self::STATUS_APPROVED => '承認済み',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breakCorrectionRequests()
    {
        return $this->hasMany(BreakCorrectionRequest::class);
    }

    public function getStatusLabel()
    {
        return self::STATUS_LABELS[$this->status];
    }

}

