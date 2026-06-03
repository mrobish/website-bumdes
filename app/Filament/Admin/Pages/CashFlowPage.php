<?php

namespace App\Filament\Admin\Pages;

use App\Models\Transaction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class CashFlowPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.cash-flow';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?string $navigationGroup = '📊 Laporan';
    protected static ?string $navigationLabel = 'Arus Kas';
    protected static ?string $title = 'Laporan Arus Kas';
    protected static ?int $navigationSort = 5;

    public ?array $data = [];
    public array $incomeByCategory = [];
    public array $expenseByCategory = [];
    public float $totalIncome = 0;
    public float $totalExpense = 0;
    public float $netCashFlow = 0;

    public function mount(): void
    {
        $this->form->fill([
            'year' => now()->year,
            'month' => now()->month,
            'unit_id' => '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)->schema([
                    Select::make('year')
                        ->label('Tahun')
                        ->options(collect(array_reverse(range(now()->year - 3, now()->year + 1)))->mapWithKeys(fn ($y) => [$y => $y]))
                        ->default(now()->year)
                        ->required(),
                    Select::make('month')
                        ->label('Bulan')
                        ->options([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                            4 => 'April', 5 => 'Mei', 6 => 'Juni',
                            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ])
                        ->default(now()->month)
                        ->required(),
                    Select::make('unit_id')
                        ->label('Unit')
                        ->options(fn () => ['' => 'Semua Unit'] + \App\Models\BusinessUnit::pluck('name', 'id')->toArray())
                        ->default(''),
                ])->columns(3),
            ])
            ->statePath('data');
    }

    public function loadData(): void
    {
        $data = $this->form->getState();
        $year = $data['year'];
        $month = $data['month'];
        $unitId = !empty($data['unit_id']) ? (int) $data['unit_id'] : null;

        // Income by category
        $incomeQuery = Transaction::where('type', 'income')
            ->where('is_void', false)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month);

        if ($unitId) {
            $incomeQuery->where('unit_id', $unitId);
        }

        $this->incomeByCategory = $incomeQuery->with('category')
            ->get()
            ->groupBy(fn ($t) => $t->category->name ?? 'Lainnya')
            ->map(fn ($group) => $group->sum('amount'))
            ->sortDesc()
            ->toArray();

        // Expense by category
        $expenseQuery = Transaction::where('type', 'expense')
            ->where('is_void', false)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month);

        if ($unitId) {
            $expenseQuery->where('unit_id', $unitId);
        }

        $this->expenseByCategory = $expenseQuery->with('category')
            ->get()
            ->groupBy(fn ($t) => $t->category->name ?? 'Lainnya')
            ->map(fn ($group) => $group->sum('amount'))
            ->sortDesc()
            ->toArray();

        $this->totalIncome = array_sum($this->incomeByCategory);
        $this->totalExpense = array_sum($this->expenseByCategory);
        $this->netCashFlow = $this->totalIncome - $this->totalExpense;
    }
}
