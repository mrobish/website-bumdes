<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessUnit extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function transactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(InterAccountTransfer::class, 'from_business_unit_id');
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(InterAccountTransfer::class, 'to_business_unit_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInduk($query)
    {
        return $query->where('type', 'induk');
    }

    public function scopeUnitUsaha($query)
    {
        return $query->where('type', 'unit_usaha');
    }

    // Helpers
    public function isInduk(): bool
    {
        return $this->type === 'induk';
    }

    public function getFullNameAttribute(): string
    {
        return $this->code . ' - ' . $this->name;
    }
}
