<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name', 'type', 'default_account_code', 'unit_id', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function unit()
    {
        return $this->belongsTo(BusinessUnit::class, 'unit_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'default_account_code', 'code');
    }

    public function overrides()
    {
        return $this->hasMany(CategoryAccountOverride::class);
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
        return $query->where('is_active', true);
    }

    public function getAccountCodeForUnit(?int $unitId): string
    {
        if ($unitId) {
            $override = $this->overrides()->where('unit_id', $unitId)->first();
            if ($override) {
                return $override->account_code;
            }
        }
        return $this->default_account_code;
    }
}
