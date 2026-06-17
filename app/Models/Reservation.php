<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Scopes\BranchScope;

class Reservation extends Model
{
    use SoftDeletes, HasFactory;

    public const SOURCE_WEB     = 'web';
    public const SOURCE_WALK_IN = 'walk_in';

    protected $fillable = [
        'customer_id',
        'employee_id',
        'branch_id',
        'source',
        'guest_name',
        'reservation_time',
        'status',
        'queue_number',
        'notes',
    ];

    protected $casts = [
        'reservation_time' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope());

        // Auto-generate nomor antrian per hari per cabang
        static::creating(function (Reservation $reservation) {
            if (empty($reservation->queue_number)) {
                $count = self::withoutGlobalScope(BranchScope::class)
                    ->where('branch_id', $reservation->branch_id)
                    ->whereDate('reservation_time', \Carbon\Carbon::parse($reservation->reservation_time)->toDateString())
                    ->count();

                $branchCode = \App\Models\Branch::find($reservation->branch_id)?->slug ?? 'Q';
                $reservation->queue_number = strtoupper(substr($branchCode, 0, 1)) . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'reservation_services');
    }

    public function scopeActiveQueue($query)
    {
        return $query->whereIn('status', ['pending', 'arrived'])
            ->whereDate('reservation_time', today());
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->customer?->name
            ?? $this->guest_name
            ?? 'Tamu';
    }
}
