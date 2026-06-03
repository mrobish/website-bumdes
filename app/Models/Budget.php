<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'category_id', 'unit_id', 'year', 'month', 'amount', 'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(BusinessUnit::class, 'unit_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getActualSpentAttribute(): float
    {
        return Transaction::where('category_id', $this->category_id)
            ->where('unit_id', $this->unit_id)
            ->whereMonth('transaction_date', $this->month)
            ->whereYear('transaction_date', $this->year)
            ->where('type', 'expense')
            ->where('is_void', false)
            ->sum('amount');
    }

    public function getUtilizationAttribute(): float
    {
        if ($this->amount <= 0) return 0;
        return ($this->actual_spent / $this->amount) * 100;
    }

    public function getStatusAttribute(): string
    {
        $util = $this->utilization;
        if ($util >= 100) return 'over';
        if ($util >= 80) return 'warning';
        return 'ok';
    }
}
