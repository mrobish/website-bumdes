<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class FiscalYear extends Model
{
    

    protected $fillable = ['year', 'status', 'closed_at', 'closed_by'];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'fiscal_year', 'year');
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'fiscal_year', 'year');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public static function getCurrent(): ?self
    {
        return self::where('year', date('Y'))->first();
    }
}
