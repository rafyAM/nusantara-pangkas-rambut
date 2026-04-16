<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'transaction_id',
        'cashier_shift_id',
        'method',
        'amount',
        'reference',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // --- Relationships ---

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function cashierShift()
    {
        return $this->belongsTo(CashierShift::class);
    }

    // --- Scopes ---

    public function scopeCash($query)
    {
        return $query->where('method', 'cash');
    }

    public function scopeNonCash($query)
    {
        return $query->where('method', '!=', 'cash');
    }
}
