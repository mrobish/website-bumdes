<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class FinancialTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'type',
        'account_id',
        'counter_account_id',
        'amount',
        'description',
        'reference',
        'attachment',
        'business_unit_id',
        'attachments',
        'created_by',
        'approved_by',
        'status',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'attachments' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (FinancialTransaction $transaction) {
            if (empty($transaction->transaction_number)) {
                $transaction->transaction_number = self::generateNumber();
            }
        });
    }

    // Generate transaction number
    public static function generateNumber(): string
    {
        $prefix = 'TRX';
        $date = now()->format('Ymd');
        $last = self::where('transaction_number', 'like', "{$prefix}{$date}%")
            ->orderByDesc('transaction_number')
            ->first();
        
        if ($last) {
            $sequence = intval(substr($last->transaction_number, -4)) + 1;
        } else {
            $sequence = 1;
        }
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // Relationship: account
    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    // Relationship: counter account
    public function counterAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'counter_account_id');
    }

    // Relationship: business unit
    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    // Relationship: creator
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship: approver
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scope: approved only
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Scope: by type
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope: by date range
    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('transaction_date', [$start, $end]);
    }

    // Scope: current year
    public function scopeCurrentYear($query)
    {
        return $query->whereYear('transaction_date', now()->year);
    }

    // Scope: by business unit
    public function scopeForUnit($query, int $unitId)
    {
        return $query->where('business_unit_id', $unitId);
    }

    // Get formatted amount
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    // Check if can be edited
    public function getCanEditAttribute()
    {
        return $this->status === 'draft';
    }

    // Check if can be approved
    public function getCanApproveAttribute()
    {
        return $this->status === 'pending';
    }
}
