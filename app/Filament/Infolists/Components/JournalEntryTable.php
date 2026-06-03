<?php

namespace App\Filament\Infolists\Components;

use Filament\Infolists\Components\Entry;
use Illuminate\Support\Facades\DB;

class JournalEntryTable extends Entry
{
    protected string $view = 'filament.infolists.components.journal-entry-table';

    protected ?\App\Models\Transaction $transaction = null;

    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'journal_entries');
    }

    public function transaction(\App\Models\Transaction $transaction): static
    {
        $this->transaction = $transaction;
        return $this;
    }

    public function getTransaction(): ?\App\Models\Transaction
    {
        return $this->transaction ?? $this->getRecord();
    }
}
