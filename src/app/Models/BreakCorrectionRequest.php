<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakCorrectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'correction_request_id',
        'request_break_start_at',
        'request_break_end_at',
    ];

    protected $casts = [
        'request_break_start_at' => 'datetime',
        'request_break_end_at' => 'datetime',
    ];

    public function correctionRequests()
    {
        return $this->belongsTo(CorrectionRequest::class);
    }
}
