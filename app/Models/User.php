<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles, HasPushSubscriptions;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function currentBranch(): ?Branch
    {
        return $this->branches()->first();
    }

    public function branchIds(): array
    {
        return Cache::remember(
            "user_{$this->id}_branch_ids",
            now()->addHours(6),
            fn () => $this->branches()->pluck('branches.id')->toArray()
        );
    }

    public function clearPermissionCache(): void
    {
        Cache::forget("user_{$this->id}_branch_ids");
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin', 'cashier']);
    }

    public function cashierShifts()
    {
        return $this->hasMany(CashierShift::class);
    }
}