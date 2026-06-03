<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InterAccountTransfer extends Model
{
    protected $fillable = [
        'transfer_number',
        'transfer_date',
        'from_business_unit_id',
        'from_account_id',
        'to_business_unit_id',
        'to_account_id',
        'amount',
        'description',
        'notes',
        'status',
        'approved_by',
        'approved_at',
        'attachments',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'attachments' => 'array',
    ];

    // Relationships
    public function fromBusinessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class, 'from_business_unit_id');
    }

    public function toBusinessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class, 'to_business_unit_id');
    }

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'to_account_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForUnit($query, int $unitId)
    {
        return $query->where(function ($q) use ($unitId) {
            $q->where('from_business_unit_id', $unitId)
              ->orWhere('to_business_unit_id', $unitId);
        });
    }

    // Auto-generate transfer number
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->transfer_number)) {
                $year = date('Y');
                $last = self::whereYear('created_at', $year)->count() + 1;
                $model->transfer_number = 'RAK-' . $year . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Helpers
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function approve(int $userId): bool
    {
        return $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function complete(): bool
    {
        return $this->update(['status' => 'completed']);
    }

    public function reject(): bool
    {
        return $this->update(['status' => 'rejected']);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}
