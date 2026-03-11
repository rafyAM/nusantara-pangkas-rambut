<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'item_type',
        'service_id',
        'product_id',
        'employee_id',
        'quantity',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (TransactionItem $item) {
            $item->subtotal = (float) ($item->price * $item->quantity);
        });
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function employee() // as barber
    {
        return $this->belongsTo(Employee::class);
    }
}
