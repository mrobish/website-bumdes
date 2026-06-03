<?php

namespace App\Filament\Admin\Pages;

use App\Models\Transaction;
use App\Models\JournalEntry;
use App\Models\Account;
use App\Models\Category;
use App\Models\Budget;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class DashboardChartsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'Dashboard Keuangan';
    protected static ?string $slug = 'dashboard-charts';
    protected static ?int $navigationSort = 0;
    protected static string $view = 'filament.pages.dashboard-charts';

    public ?string $period = null;

    // Summary data
    public float $totalPemasukan = 0;
    public float $totalPengeluaran = 0;
    public float $saldoKas = 0;
    public int $jumlahTransaksi = 0;

    // Chart data
    public array $monthlyData = [];
    public array $recentTransactions = [];
    public array $topCategories = [];
    public array $budgetStatus = [];

    public function mount(): void
    {
        $this->period = now()->format('Y-m');
        $this->loadData();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('period')
                    ->label('Periode')
                    ->options($this->getPeriodOptions())
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadData())
                    ->required(),
            ]);
    }

    protected function getPeriodOptions(): array
    {
        $options = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $options[$date->format('Y-m')] = $date->format('F Y');
        }
        return $options;
    }

    public function loadData(): void
    {
        $this->loadSummary();
        $this->loadMonthlyData();
        $this->loadRecentTransactions();
        $this->loadTopCategories();
        $this->loadBudgetStatus();
    }

    protected function loadSummary(): void
    {
        $year = Carbon::parse($this->period . '-01')->year;
        $month = Carbon::parse($this->period . '-01')->month;

        $this->totalPemasukan = Transaction::where('type', 'income')
            ->where('is_void', false)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->sum('amount');

        $this->totalPengeluaran = Transaction::where('type', 'expense')
            ->where('is_void', false)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->sum('amount');

        $this->saldoKas = $this->totalPemasukan - $this->totalPengeluaran;

        $this->jumlahTransaksi = Transaction::where('is_void', false)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->count();
    }

    protected function loadMonthlyData(): void
    {
        $this->monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $pemasukan = Transaction::where('type', 'income')
                ->where('is_void', false)
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');

            $pengeluaran = Transaction::where('type', 'expense')
                ->where('is_void', false)
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');

            $this->monthlyData[] = [
                'month' => $date->format('M Y'),
                'short_month' => $date->format('M'),
                'pemasukan' => (float) $pemasukan,
                'pengeluaran' => (float) $pengeluaran,
            ];
        }
    }

    protected function loadRecentTransactions(): void
    {
        $this->recentTransactions = Transaction::where('is_void', false)
            ->with('category', 'unit', 'creator')
            ->latest('transaction_date')
            ->limit(10)
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'number' => $t->transaction_number,
                'date' => $t->transaction_date->format('d/m/Y'),
                'type' => $t->type,
                'type_label' => $t->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                'amount' => number_format($t->amount, 0, ',', '.'),
                'description' => $t->description ?? '-',
                'category' => $t->category->name ?? '-',
                'unit' => $t->unit->name ?? '-',
                'creator' => $t->creator->name ?? '-',
            ])
            ->toArray();
    }

    protected function loadTopCategories(): void
    {
        $year = Carbon::parse($this->period . '-01')->year;
        $month = Carbon::parse($this->period . '-01')->month;

        $this->topCategories = Transaction::where('type', 'income')
            ->where('is_void', false)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(transactions.amount) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($c) => [
                'name' => $c->name,
                'total' => number_format($c->total, 0, ',', '.'),
                'raw_total' => (float) $c->total,
            ])
            ->toArray();
    }

    protected function loadBudgetStatus(): void
    {
        $year = Carbon::parse($this->period . '-01')->year;
        $month = (int) Carbon::parse($this->period . '-01')->month;

        $budgets = Budget::where('year', $year)
            ->where('month', $month)
            ->with('category')
            ->get();

        $overBudget = 0;
        $warning = 0;
        $ok = 0;

        foreach ($budgets as $budget) {
            $status = $budget->status;
            if ($status === 'over') $overBudget++;
            elseif ($status === 'warning') $warning++;
            else $ok++;
        }

        $this->budgetStatus = [
            'total' => $budgets->count(),
            'over' => $overBudget,
            'warning' => $warning,
            'ok' => $ok,
        ];
    }

    public function getMaxMonthlyAmount(): float
    {
        if (empty($this->monthlyData)) return 1;
        $max = 0;
        foreach ($this->monthlyData as $m) {
            if ($m['pemasukan'] > $max) $max = $m['pemasukan'];
            if ($m['pengeluaran'] > $max) $max = $m['pengeluaran'];
        }
        return max($max, 1);
    }
}
