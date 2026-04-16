<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'employee_id',
        'branch_id',
        'cashier_shift_id',
        'transaction_date',
        'total_amount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'payment_method',
        'status',
        'notes',
    ];

    protected $casts = [
        'transaction_date'  => 'datetime',
        'total_amount'      => 'decimal:2',
        'discount_value'    => 'decimal:2',
        'discount_amount'   => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope());

        static::creating(function (Transaction $transaction) {
            if (empty($transaction->invoice_number)) {
                $transaction->invoice_number = self::generateInvoiceNumber();
            }
        });
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date   = now()->format('Ymd');

        $lastTransaction = self::withoutGlobalScope(BranchScope::class)
            ->whereDate('created_at', today())
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $sequence = 1;
        if ($lastTransaction && preg_match('/(\d+)$/', $lastTransaction->invoice_number, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
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

    public function cashierShift()
    {
        return $this->belongsTo(CashierShift::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function recalculateTotal(): void
    {
        $this->update([
            'total_amount' => $this->items()->sum('subtotal'),
        ]);
    }
}
