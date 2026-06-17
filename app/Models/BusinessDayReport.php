<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessDayReport extends Model
{
    use HasFactory;

    public const STATUS_OPEN   = 'open';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'branch_id',
        'business_date',
        'opened_at',
        'closed_at',
        'total_orders',
        'total_gross',
        'total_cash',
        'total_non_cash',
        'total_komisi_kapster',
        'total_fee_kasir',
        'total_owner_net',
        'total_cash_out_operasional',
        'modal_awal_hari',
        'expected_kas_akhir',
        'actual_kas_akhir',
        'selisih_kas',
        'status',
        'closed_by',
        'snapshot',
        'notes',
    ];

    protected $casts = [
        'business_date'              => 'date',
        'opened_at'                  => 'datetime',
        'closed_at'                  => 'datetime',
        'total_orders'               => 'integer',
        'total_gross'                => 'decimal:2',
        'total_cash'                 => 'decimal:2',
        'total_non_cash'             => 'decimal:2',
        'total_komisi_kapster'       => 'decimal:2',
        'total_fee_kasir'            => 'decimal:2',
        'total_owner_net'            => 'decimal:2',
        'total_cash_out_operasional' => 'decimal:2',
        'modal_awal_hari'            => 'decimal:2',
        'expected_kas_akhir'         => 'decimal:2',
        'actual_kas_akhir'           => 'decimal:2',
        'selisih_kas'                => 'decimal:2',
        'snapshot'                   => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope());
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('business_date', $date);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }
}
