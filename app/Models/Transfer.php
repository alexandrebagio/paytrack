<?php

namespace App\Models;

use App\Models\Scopes\TransferScope;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'payer',
        'payee',
        'wallet_payer',
        'wallet_payee',
        'situation',
        'error',
        'error_message',
        'value',
    ];

    protected $casts = [
        'value' => 'float',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new TransferScope());
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer', 'id');
    }

    public function payee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee', 'id');
    }

    public function wallet_payer(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_payer', 'id');
    }

    public function wallet_payee(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_payee', 'id');
    }
}
