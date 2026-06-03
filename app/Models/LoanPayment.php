<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanPayment extends Model
{
    protected $fillable = [
        'capital_contribution_id', 'payment_date', 'amount', 'principal',
        'interest', 'remaining_balance', 'payment_method', 'reference_number',
        'notes', 'status',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'principal' => 'decimal:2',
        'interest' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
    ];

    public function capitalContribution(): BelongsTo
    {
        return $this->belongsTo(CapitalContribution::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}
