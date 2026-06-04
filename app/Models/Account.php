<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'code', 'name', 'type', 'normal_balance', 'parent_id',
        'is_system', 'is_active', 'description'
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'account_code', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function getBalanceAttribute(): float
    {
        // If pre-calculated via withAggregate, use that
        if (isset($this->attributes['journal_debit_sum'])) {
            $debit = (float) ($this->attributes['journal_debit_sum'] ?? 0);
            $credit = (float) ($this->attributes['journal_credit_sum'] ?? 0);
            if ($this->normal_balance === 'debit') {
                return $debit - $credit;
            }
            return $credit - $debit;
        }

        // Fallback: query individually
        $debit = $this->journalEntries()->sum('debit');
        $credit = $this->journalEntries()->sum('credit');
        
        if ($this->normal_balance === 'debit') {
            return $debit - $credit;
        }
        return $credit - $debit;
    }

    public function getDisplayCodeAttribute(): string
    {
        return $this->code . ' — ' . $this->name;
    }
}
