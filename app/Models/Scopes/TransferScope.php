<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TransferScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where(
            fn ($q) => $q->where('payer', auth()->user()->id)
                ->orWhere('payee', auth()->user()->id)
        );
    }
}
