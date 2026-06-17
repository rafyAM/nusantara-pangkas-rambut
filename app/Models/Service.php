<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'commission_owner_pct',
        'commission_kapster_pct',
        'description',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'commission_owner_pct' => 'decimal:2',
        'commission_kapster_pct' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function commissionKapsterAmount(?float $price = null): float
    {
        $base = $price ?? (float) $this->price;
        return round($base * ((float) $this->commission_kapster_pct) / 100, 2);
    }

    public function commissionOwnerAmount(?float $price = null): float
    {
        $base = $price ?? (float) $this->price;
        return round($base * ((float) $this->commission_owner_pct) / 100, 2);
    }
}
