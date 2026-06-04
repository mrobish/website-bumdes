<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HutangPayment extends Model
{
    protected $fillable = [
        'hutang_id', 'payment_date', 'amount', 'payment_method',
        'reference_number', 'notes', 'transaction_id', 'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function hutang()
    {
        return $this->belongsTo(Hutang::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
