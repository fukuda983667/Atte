<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'clock_in',
        'clock_out'
    ];

    // N対1
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 1対N
    public function breakRecords()
    {
        return $this->hasMany(BreakRecord::class);
    }
}
