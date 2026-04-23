<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'address',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function schedules()
    {
        return $this->hasMany(BranchSchedule::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'branch_service')
            ->withPivot('price_override', 'is_active')
            ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Kembalikan harga layanan untuk cabang ini (override atau default).
     */
    public function priceForService(Service $service): float
    {
        $pivot = $this->services()->where('service_id', $service->id)->first()?->pivot;
        return (float) ($pivot?->price_override ?? $service->price);
    }
}