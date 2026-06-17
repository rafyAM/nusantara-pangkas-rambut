<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashMovement extends Model
{
    use HasFactory;

    public const CATEGORY_GR_KAPSTER  = 'gr_kapster';
    public const CATEGORY_FEE_KASIR   = 'fee_kasir';
    public const CATEGORY_OPERASIONAL = 'operasional';
    public const CATEGORY_OTHER       = 'other';

    public const CATEGORIES = [
        self::CATEGORY_GR_KAPSTER  => 'GR Kapster',
        self::CATEGORY_FEE_KASIR   => 'Fee Kasir',
        self::CATEGORY_OPERASIONAL => 'Operasional',
        self::CATEGORY_OTHER       => 'Lainnya',
    ];

    protected $fillable = [
        'cashier_shift_id',
        'user_id',
        'type',
        'amount',
        'reason',
        'category',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (CashMovement $movement) {
            $shift = CashierShift::find($movement->cashier_shift_id);
            if (!$shift || $shift->status !== 'open') {
                throw new \RuntimeException('Tidak bisa menambah cash movement: shift tidak aktif.');
            }
        });
    }

    // --- Relationships ---

    public function cashierShift()
    {
        return $this->belongsTo(CashierShift::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
