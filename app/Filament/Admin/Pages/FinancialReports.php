<?php

namespace App\Filament\Admin\Pages;

use App\Models\ChartOfAccount;
use App\Models\FinancialTransaction;
use App\Models\BusinessUnit;
use Filament\Forms;
use Filament\Pages\Page;

class FinancialReports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Laporan Keuangan';
    protected static ?string $title = 'Laporan Keuangan BUMDes';
    protected static ?int $navigationSort = 11;
    protected static string $view = 'filament.admin.pages.financial-reports';

    public $period = '';
    public $activeTab = 'neraca';
    
    // Neraca data
    public $assets = ['current' => 0, 'fixed' => 0, 'total' => 0];
    public $liabilities = ['current' => 0, 'long_term' => 0, 'total' => 0];
    public $equity = ['capital' => 0, 'retained' => 0, 'current_year' => 0, 'total' => 0];
    
    // Laba Rugi data
    public $revenue = ['operating' => 0, 'other' => 0, 'total' => 0];
    public $expenses = ['operating' => 0, 'other' => 0, 'total' => 0];
    public $netProfit = 0;
    
    // Arus Kas data
    public $cashFlow = ['operating' => 0, 'investing' => 0, 'financing' => 0, 'net' => 0, 'beginning' => 0, 'ending' => 0];
    
    // CALK data
    public $calkNotes = [];

    public function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Parameter Laporan')
                ->schema([
                    Forms\Components\Select::make('period')
                        ->label('Periode')
                        ->options(fn () => collect(range(1, 12))
                            ->mapWithKeys(fn ($m) => [
                                now()->year . '-' . str_pad($m, 2, '0', STR_PAD_LEFT) => now()->year . '-' . str_pad($m, 2, '0', STR_PAD_LEFT),
                            ]))
                        ->default(date('Y-m'))
                        ->required(),
                    Forms\Components\Select::make('activeTab')
                        ->label('Jenis Laporan')
                        ->options([
                            'neraca' => 'Neraca (Laporan Posisi Keuangan)',
                            'lr' => 'Laporan Laba/Rugi',
                            'arus_kas' => 'Laporan Arus Kas',
                            'calk' => 'Catatan Atas Laporan Keuangan',
                        ])->default('neraca'),
                    Forms\Components\Button::make('generate')
                        ->label('Generate Laporan')
                        ->color('primary')
                        ->action('generateReport'),
                ])->columns(3),
        ];
    }

    public function generateReport(): void
    {
        $data = $this->form->getState();
        $this->period = $data['period'] ?? date('Y-m');
        $this->activeTab = $data['activeTab'] ?? 'neraca';

        $year = substr($this->period, 0, 4);
        $month = substr($this->period, 5, 2);
        $startDate = $year . '-' . $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        // Get all transactions for the period
        $transactions = FinancialTransaction::where('status', 'posted')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        // Get all accounts
        $accounts = ChartOfAccount::where('is_group', false)->get();

        // NERACA
        $this->assets['current'] = $accounts->where('code', 'like', '11%')->sum(function ($acc) use ($transactions) {
            $debit = $transactions->where('account_id', $acc->id)->where('type', 'pemasukan')->sum('amount');
            $credit = $transactions->where('counter_account_id', $acc->id)->where('type', 'pengeluaran')->sum('amount');
            return $debit - $credit;
        });
        $this->assets['fixed'] = $accounts->where('code', 'like', '12%')->sum(function ($acc) use ($transactions) {
            $debit = $transactions->where('account_id', $acc->id)->where('type', 'pemasukan')->sum('amount');
            $credit = $transactions->where('counter_account_id', $acc->id)->where('type', 'pengeluaran')->sum('amount');
            return $debit - $credit;
        });
        $this->assets['total'] = $this->assets['current'] + $this->assets['fixed'];

        $this->liabilities['current'] = $accounts->where('code', 'like', '21%')->sum(function ($acc) use ($transactions) {
            $credit = $transactions->where('counter_account_id', $acc->id)->where('type', 'pemasukan')->sum('amount');
            $debit = $transactions->where('account_id', $acc->id)->where('type', 'pengeluaran')->sum('amount');
            return $credit - $debit;
        });
        $this->liabilities['total'] = $this->liabilities['current'];

        $this->equity['capital'] = $accounts->where('code', 'like', '3101')->sum(function ($acc) use ($transactions) {
            $credit = $transactions->where('counter_account_id', $acc->id)->where('type', 'pemasukan')->sum('amount');
            return $credit;
        });

        // LABA RUGI
        $this->revenue['operating'] = $accounts->where('code', 'like', '41%')->sum(function ($acc) use ($transactions) {
            return $transactions->where('account_id', $acc->id)->where('type', 'pemasukan')->sum('amount');
        });
        $this->revenue['other'] = $accounts->where('code', 'like', '42%')->sum(function ($acc) use ($transactions) {
            return $transactions->where('account_id', $acc->id)->where('type', 'pemasukan')->sum('amount');
        });
        $this->revenue['total'] = $this->revenue['operating'] + $this->revenue['other'];

        $this->expenses['operating'] = $accounts->where('code', 'like', '51%')->sum(function ($acc) use ($transactions) {
            return $transactions->where('account_id', $acc->id)->where('type', 'pengeluaran')->sum('amount');
        });
        $this->expenses['other'] = $accounts->where('code', 'like', '52%')->sum(function ($acc) use ($transactions) {
            return $transactions->where('account_id', $acc->id)->where('type', 'pengeluaran')->sum('amount');
        });
        $this->expenses['total'] = $this->expenses['operating'] + $this->expenses['other'];

        $this->netProfit = $this->revenue['total'] - $this->expenses['total'];
        $this->equity['current_year'] = $this->netProfit;
        $this->equity['total'] = $this->equity['capital'] + $this->equity['retained'] + $this->equity['current_year'];

        // ARUS KAS (simplified)
        $this->cashFlow['operating'] = $this->netProfit;
        $this->cashFlow['net'] = $this->cashFlow['operating'] + $this->cashFlow['investing'] + $this->cashFlow['financing'];
        $this->cashFlow['ending'] = $this->cashFlow['beginning'] + $this->cashFlow['net'];
    }
}
