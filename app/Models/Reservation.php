<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\BranchScope;

class Reservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'employee_id',
        'branch_id',
        'reservation_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'reservation_time' => 'datetime',
    ];

    protected static function booted(): void {
        static::addGlobalScope(new BranchScope());
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
}
