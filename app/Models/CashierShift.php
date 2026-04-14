<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashierShift extends Model
{
    protected $fillable = [
        'user_id',
        'start_at',
        'end_at',
        'opening_cash',
        'expected_cash',
        'actual_cash',
        'difference',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_at'      => 'datetime',
        'end_at'        => 'datetime',
        'opening_cash'  => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'actual_cash'   => 'decimal:2',
        'difference'    => 'decimal:2',
    ];

    // --- Relationships ---

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function cashMovements()
    {
        return $this->hasMany(CashMovement::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // --- Scopes ---

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // --- Methods ---

    /**
     * Hitung expected cash (uang fisik yang seharusnya ada di drawer).
     * Hanya transaksi tunai yang dihitung.
     */
    public function calculateExpectedCash(): float
    {
        $cashPayments = $this->payments()->where('method', 'cash')->sum('amount');
        $cashIn       = $this->cashMovements()->where('type', 'in')->sum('amount');
        $cashOut      = $this->cashMovements()->where('type', 'out')->sum('amount');

        return (float) $this->opening_cash + $cashPayments + $cashIn - $cashOut;
    }

    /**
     * Tutup shift: hitung expected, simpan actual, hitung selisih.
     */
    public function close(float $actualCash, ?string $notes = null): void
    {
        $expectedCash = $this->calculateExpectedCash();

        $this->update([
            'end_at'        => now(),
            'expected_cash' => $expectedCash,
            'actual_cash'   => $actualCash,
            'difference'    => $actualCash - $expectedCash,
            'status'        => 'closed',
            'notes'         => $notes,
        ]);
    }

    /**
     * Ringkasan penjualan per metode pembayaran.
     */
    public function paymentSummary(): array
    {
        return $this->payments()
            ->selectRaw('method, SUM(amount) as total, COUNT(DISTINCT transaction_id) as count')
            ->groupBy('method')
            ->get()
            ->keyBy('method')
            ->toArray();
    }
}
