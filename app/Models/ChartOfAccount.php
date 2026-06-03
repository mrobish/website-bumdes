<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChartOfAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'parent_id',
        'is_group',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_group' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationship: parent
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    // Relationship: children
    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    // Relationship: transactions
    public function transactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'account_id');
    }

    // Relationship: budgets
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class, 'account_id');
    }

    // Scope: active only
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope: by type
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope: root accounts (no parent)
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    // Get balance (debit - credit)
    public function getBalanceAttribute()
    {
        $debit = $this->transactions()
            ->where('type', 'pemasukan')
            ->where('status', 'approved')
            ->sum('amount');
        
        $credit = $this->transactions()
            ->where('type', 'pengeluaran')
            ->where('status', 'approved')
            ->sum('amount');
        
        // For asset and expense accounts: debit - credit
        // For liability, equity, and revenue accounts: credit - debit
        if (in_array($this->type, ['aset', 'beban'])) {
            return $debit - $credit;
        }
        
        return $credit - $debit;
    }

    // Get formatted balance
    public function getFormattedBalanceAttribute()
    {
        return 'Rp ' . number_format($this->balance, 0, ',', '.');
    }

    // Get full code (with parent)
    public function getFullCodeAttribute()
    {
        if ($this->parent) {
            return $this->parent->code . '.' . $this->code;
        }
        return $this->code;
    }

    // Accessor untuk code_name (code - name)
    public function getCodeNameAttribute(): string
    {
        return $this->code . ' - ' . $this->name;
    }
}

