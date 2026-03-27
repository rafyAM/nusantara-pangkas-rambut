<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BranchScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        if ($user->hasRole('super_admin')) {
            return;
        }

        $branchIds = $user->branches()->pluck('branches.id');

        if ($branchIds->isEmpty()) {
            $builder->whereRaw('1 = 0');
            return;
        }

        $builder->whereIn($model->getTable() . '.branch_id', $branchIds);
    }
}
