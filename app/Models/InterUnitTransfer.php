<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterUnitTransfer extends Model
{
    protected $fillable = [
        'from_unit_id', 'to_unit_id', 'amount', 'description',
        'status', 'created_by', 'confirmed_by', 'confirmed_at',
        'journal_entry_sender_id', 'journal_entry_receiver_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
    ];

    public function fromUnit()
    {
        return $this->belongsTo(BusinessUnit::class, 'from_unit_id');
    }

    public function toUnit()
    {
        return $this->belongsTo(BusinessUnit::class, 'to_unit_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function senderEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_sender_id');
    }

    public function receiverEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_receiver_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }
}
