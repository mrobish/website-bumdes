<?php

namespace App\Filament\Admin\Pages;

use App\Models\Account;
use App\Services\AutoJournalService;
use Filament\Pages\Page;

class TrialBalancePage extends Page
{
    protected static string $view = 'filament.pages.trial-balance';
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationGroup = '📊 Laporan';
    protected static ?string $navigationLabel = 'Neraca Saldo';
    protected static ?string $title = 'Neraca Saldo';
    protected static ?int $navigationSort = 2;

    public int $year = 0;
    public int $month = 0;
    public ?int $unitId = null;
    public bool $loaded = false;

    public array $accounts = [];
    public float $totalDebit = 0;
    public float $totalCredit = 0;
    public bool $isBalanced = false;

    public function mount(): void
    {
        $this->year = (int) now()->year;
        $this->month = (int) now()->month;
    }

    public function loadData(): void
    {
        $year = $this->year;
        $month = $this->month > 0 ? $this->month : null;
        $unitId = $this->unitId;

        $accounts = Account::active()->orderBy('code')->get();
        $result = [];

        foreach ($accounts as $account) {
            $balance = AutoJournalService::getAccountBalance($account->code, $unitId, $year, $month);
            if (abs($balance) > 0.01) {
                $result[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'debit' => $account->normal_balance === 'debit' ? max($balance, 0) : 0,
                    'credit' => $account->normal_balance === 'credit' ? max($balance, 0) : 0,
                ];
            }
        }

        $this->accounts = $result;
        $this->totalDebit = collect($result)->sum('debit');
        $this->totalCredit = collect($result)->sum('credit');
        $this->isBalanced = abs($this->totalDebit - $this->totalCredit) < 0.01;
        $this->loaded = true;
    }
}
