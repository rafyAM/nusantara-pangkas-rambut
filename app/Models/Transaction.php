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
                $transaction->invoice_number = self::generateInvoiceNumber($transaction->branch_id);
            }
        });

        // Kembalikan stok produk saat transaksi dibatalkan
        static::updated(function (Transaction $transaction) {
            if ($transaction->wasChanged('status') && $transaction->status === 'cancelled') {
                foreach ($transaction->items()->where('item_type', 'product')->get() as $item) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }
        });
    }

    public static function generateInvoiceNumber(?int $branchId = null, int $attempt = 1): string
    {
        $maxAttempts = 5;
        
        $date = now()->format('Ymd');
        $time = now()->format('His'); 

        $branchCode = 'GEN';
        if ($branchId) {
            $branch = Branch::find($branchId);
            if ($branch) {
                $branchCode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $branch->slug ?? $branch->name), 0, 3));
            }
        }

        try {
        $lastTransaction = self::withoutGlobalScope(BranchScope::class)
            ->where('branch_id', $branchId)
            ->whereDate('created_at', today())
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $sequence = 1;
        if ($lastTransaction && preg_match('/(\d+)$/', $lastTransaction->invoice_number, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        $invoiceNumber = sprintf('INV-%s-%s-%s-%04d', $branchCode, $date, $time, $sequence);
        
        if (self::withoutGlobalScope(BranchScope::class)->where('invoice_number', $invoiceNumber)->exists()) {
            if ($attempt < $maxAttempts) {
                usleep(mt_rand(100, 500) * 1000); // Sleep 100-500ms
                return self::generateInvoiceNumber($branchId, $attempt + 1);
            }
            
            $random = str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);
            $invoiceNumber = sprintf('INV-%s-%s-%s-%04d-%s', $branchCode, $date, $time, $sequence, $random);
        }

        return $invoiceNumber;
        } catch (\Exception $e) {
            $randomPart = strtoupper(substr(md5(uniqid()), 0, 6));
            return sprintf('INV-%s-%s-%s-%s', $branchCode, $date, $time, $randomPart);
        }

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