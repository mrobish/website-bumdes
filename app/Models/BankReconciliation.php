<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankReconciliation extends Model
{
    protected $fillable = [
        'transaction_id', 'bank_date', 'bank_amount', 'bank_description',
        'match_status', 'reconciled_by', 'reconciled_at'
    ];

    protected $casts = [
        'bank_date' => 'date',
        'bank_amount' => 'decimal:2',
        'reconciled_at' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function reconciledByUser()
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }

    public function isMatched(): bool
    {
        return $this->match_status === 'matched';
    }
}
