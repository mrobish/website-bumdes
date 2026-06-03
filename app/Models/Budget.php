<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'year',
        'planned_amount',
        'actual_amount',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
        'planned_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
    ];

    // Relationship: account
    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    // Scope: by year
    public function scopeYear($query, $year)
    {
        return $query->where('year', $year);
    }

    // Get realization percentage
    public function getRealizationPercentAttribute()
    {
        if ($this->planned_amount <= 0) {
            return 0;
        }
        return round(($this->actual_amount / $this->planned_amount) * 100, 2);
    }

    // Get remaining budget
    public function getRemainingAttribute()
    {
        return $this->planned_amount - $this->actual_amount;
    }

    // Get formatted values
    public function getFormattedPlannedAttribute()
    {
        return 'Rp ' . number_format($this->planned_amount, 0, ',', '.');
    }

    public function getFormattedActualAttribute()
    {
        return 'Rp ' . number_format($this->actual_amount, 0, ',', '.');
    }

    public function getFormattedRemainingAttribute()
    {
        return 'Rp ' . number_format($this->remaining, 0, ',', '.');
    }
}
