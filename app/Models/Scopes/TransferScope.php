<?php

namespace App\Models\Scopes;

use App\Enums\TransferSituation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TransferScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $userId = auth()->user()->id;

        $builder->where(
            fn ($q) => $q->where('payer', $userId)
                ->orWhere(
                    fn ($qs) => $qs->where('payee', $userId)
                        ->where('situation', TransferSituation::Finish->value)
                )
        );
    }
}
