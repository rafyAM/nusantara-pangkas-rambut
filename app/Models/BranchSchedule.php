<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchSchedule extends Model
{
    protected $fillable = [
        'branch_id',
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
    ];

    protected $casts = [
        'is_closed'   => 'boolean',
        'day_of_week' => 'integer',
    ];

    public static array $dayNames = [
        0 => 'Minggu',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getDayNameAttribute(): string
    {
        return self::$dayNames[$this->day_of_week] ?? '-';
    }
}
