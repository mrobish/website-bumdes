<?php

namespace App\Filament\Admin\Pages;

use App\Models\Account;
use App\Services\AutoJournalService;
use Filament\Pages\Page;

class ConsolidatedReportPage extends Page
{
    protected static string $view = 'filament.pages.consolidated-report';
    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = '📊 Laporan';
    protected static ?string $navigationLabel = 'Konsolidasi';
    protected static ?string $title = 'Laporan Konsolidasi';
    protected static ?int $navigationSort = 6;

    public int $year = 0;
    public bool $loaded = false;

    public array $units = [];
    public float $totalCash = 0;
    public float $totalRevenue = 0;
    public float $totalExpense = 0;
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
        $units = \App\Models\BusinessUnit::all();
        $result = [];

        foreach ($units as $unit) {
            $cash = AutoJournalService::getAccountBalance('1101', $unit->id, $year);
            $revenue = 0;
            $expense = 0;

            // Revenue
            foreach (Account::where('type', 'revenue')->pluck('code') as $code) {
                $revenue += AutoJournalService::getAccountBalance($code, $unit->id, $year);
            }

            // Expense
            foreach (Account::where('type', 'expense')->pluck('code') as $code) {
                $expense += AutoJournalService::getAccountBalance($code, $unit->id, $year);
            }

            if (abs($cash) > 0.01 || abs($revenue) > 0.01 || abs($expense) > 0.01) {
                $result[] = [
                    'name' => $unit->name,
                    'cash' => $cash,
                    'revenue' => $revenue,
                    'expense' => $expense,
                    'net' => $revenue - $expense,
                ];
            }
        }

        $this->units = $result;

        // Consolidated totals
        $this->totalCash = collect($result)->sum('cash');
        $this->totalRevenue = collect($result)->sum('revenue');
        $this->totalExpense = collect($result)->sum('expense');
        $this->totalAsset = AutoJournalService::getTotalByType('asset', null, $year);
        $this->totalLiability = AutoJournalService::getTotalByType('liability', null, $year);
        $this->totalEquity = AutoJournalService::getTotalByType('equity', null, $year);

        $this->loaded = true;
    }
}
