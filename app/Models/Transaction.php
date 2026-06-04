<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'transaction_number', 'transaction_date', 'unit_id', 'type',
        'category_id', 'amount', 'description', 'reference_number',
        'attachment_path', 'created_by', 'fiscal_year', 'is_void',
        'void_reason', 'voided_by', 'voided_at', 'approved_by',
        'approved_at', 'is_locked'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'is_void' => 'boolean',
        'is_locked' => 'boolean',
        'voided_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function unit()
    {
        return $this->belongsTo(BusinessUnit::class, 'unit_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function voidedBy()
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeActive($query)
    {
        return $query->where('is_void', false);
    }

    public function scopeForYear($query, $year)
    {
        return $query->where('fiscal_year', $year);
    }

    public function scopeForUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function isVoided(): bool
    {
        return $this->is_void;
    }

    public function canBeEdited(): bool
    {
        return !$this->is_locked && !$this->is_void;
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path ? asset('storage/' . $this->attachment_path) : null;
    }

    /**
     * Generate transaction number with DB lock to prevent duplicates
     */
    public static function generateNumber(string $prefix = 'TRX'): string
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($prefix) {
            $date = date('Ymd');
            $last = static::where('transaction_number', 'like', "{$prefix}-{$date}-%")
                ->lockForUpdate()
                ->orderByDesc('transaction_number')
                ->first();

            if ($last) {
                $sequence = intval(substr($last->transaction_number, -3)) + 1;
            } else {
                $sequence = 1;
            }

            return "{$prefix}-{$date}-" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
        });
    }
}
