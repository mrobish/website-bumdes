<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'transaction_id', 'entry_date', 'entry_type', 'account_code',
        'debit', 'credit', 'unit_id', 'description', 'fiscal_year',
        'is_closing_entry'
    ];

    protected $casts = [
        'entry_date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'is_closing_entry' => 'boolean',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_code', 'code');
    }

    public function unit()
    {
        return $this->belongsTo(BusinessUnit::class, 'unit_id');
    }

    public function scopeForAccount($query, $code)
    {
        return $query->where('account_code', $code);
    }

    public function scopeForUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopeForYear($query, $year)
    {
        return $query->where('fiscal_year', $year);
    }

    public function scopeNormal($query)
    {
        return $query->where('entry_type', 'normal');
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('entry_date', [$from, $to]);
    }
}
