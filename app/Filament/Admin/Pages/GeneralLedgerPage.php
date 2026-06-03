<?php

namespace App\Filament\Admin\Pages;

use App\Models\Account;
use App\Models\JournalEntry;
use Filament\Pages\Page;

class GeneralLedgerPage extends Page
{
    protected static string $view = 'filament.pages.general-ledger';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = '📊 Laporan';
    protected static ?string $navigationLabel = 'Buku Besar';
    protected static ?string $title = 'Buku Besar';
    protected static ?int $navigationSort = 1;

    public string $accountCode = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public ?int $unitId = null;
    public bool $loaded = false;

    public array $entries = [];
    public float $totalDebit = 0;
    public float $totalCredit = 0;
    public float $balance = 0;

    public function mount(): void
    {
        $this->dateFrom = now()->startOfYear()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function loadData(): void
    {
        $query = JournalEntry::query()
            ->where('account_code', $this->accountCode)
            ->whereBetween('entry_date', [$this->dateFrom, $this->dateTo]);

        if ($this->unitId) {
            $query->where('unit_id', $this->unitId);
        }

        $this->entries = $query->with('transaction', 'unit')
            ->orderBy('entry_date')
            ->get()
            ->toArray();

        $this->totalDebit = collect($this->entries)->sum('debit');
        $this->totalCredit = collect($this->entries)->sum('credit');
        
        $account = Account::where('code', $this->accountCode)->first();
        $this->balance = ($account && $account->normal_balance === 'debit')
            ? $this->totalDebit - $this->totalCredit
            : $this->totalCredit - $this->totalDebit;
        $this->loaded = true;
    }
}
