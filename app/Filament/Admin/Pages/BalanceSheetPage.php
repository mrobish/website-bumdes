<?php

namespace App\Filament\Admin\Pages;

use App\Models\Account;
use App\Services\AutoJournalService;
use Filament\Pages\Page;

class BalanceSheetPage extends Page
{
    protected static string $view = 'filament.pages.balance-sheet';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = '📊 Laporan';
    protected static ?string $navigationLabel = 'Neraca';
    protected static ?string $title = 'Laporan Neraca';
    protected static ?int $navigationSort = 4;

    public int $year = 0;
    public ?int $unitId = null;
    public bool $loaded = false;

    public array $assets = [];
    public array $liabilities = [];
    public array $equity = [];
    public float $totalAsset = 0;
    public float $totalLiability = 0;
    public float $totalEquity = 0;

    public function mount(): void
    {
        $this->year = (int) now()->year;
    }

    public function loadData(): void
    {
        $year = $this->year;
        $unitId = $this->unitId;

        $assetAccounts = Account::where('type', 'asset')->active()->orderBy('code')->get();
        $this->assets = [];
        foreach ($assetAccounts as $account) {
            $balance = AutoJournalService::getAccountBalance($account->code, $unitId, $year);
            if (abs($balance) > 0.01) {
                $this->assets[] = ['code' => $account->code, 'name' => $account->name, 'amount' => abs($balance)];
            }
        }

        $liabilityAccounts = Account::where('type', 'liability')->active()->orderBy('code')->get();
        $this->liabilities = [];
        foreach ($liabilityAccounts as $account) {
            $balance = AutoJournalService::getAccountBalance($account->code, $unitId, $year);
            if (abs($balance) > 0.01) {
                $this->liabilities[] = ['code' => $account->code, 'name' => $account->name, 'amount' => abs($balance)];
            }
        }

        $equityAccounts = Account::where('type', 'equity')->active()->orderBy('code')->get();
        $this->equity = [];
        foreach ($equityAccounts as $account) {
            $balance = AutoJournalService::getAccountBalance($account->code, $unitId, $year);
            if (abs($balance) > 0.01) {
                $this->equity[] = ['code' => $account->code, 'name' => $account->name, 'amount' => abs($balance)];
            }
        }

        $this->totalAsset = collect($this->assets)->sum('amount');
        $this->totalLiability = collect($this->liabilities)->sum('amount');
        $this->totalEquity = collect($this->equity)->sum('amount');
        $this->loaded = true;
    }
}
