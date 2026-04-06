<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;


use Illuminate\Support\Facades\Auth;

class BranchScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        /** @var \App\Models\User|\App\Models\Customer|null $user */
        $user = Auth::user();

        if (! $user) {
            return;
        }

        if ($user->hasRole('super_admin')) {
            return;
        }

        // Pastikan model user memiliki method branches()
        // karena jika login sebagai Customer, method ini tidak ada.
        if (! method_exists($user, 'branches')) {
            return;
        }

        $branchIds = $user->branches()->pluck('branches.id')->toArray();

        // nambah juga dari relasi Employee jika memiliki
        // pake DB::table buat mencegah Infinite Loop (karena model Employee juga pake BranchScope)
        $employee = \Illuminate\Support\Facades\DB::table('employees')
            ->where('user_id', $user->id)
            ->first();

        if ($employee && $employee->branch_id) {
            $branchIds[] = $employee->branch_id;
        }

        if (empty($branchIds)) {
            $builder->whereRaw('1 = 0');
            return;
        }

        $builder->whereIn($model->getTable() . '.branch_id', $branchIds);
    }
}
