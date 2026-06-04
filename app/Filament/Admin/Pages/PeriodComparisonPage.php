<?php

namespace App\Filament\Admin\Pages;

use App\Services\AutoJournalService;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

class PeriodComparisonPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.period-comparison';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationGroup = '📊 Laporan';
    protected static ?string $navigationLabel = 'Perbandingan Periode';
    protected static ?string $title = 'Perbandingan Laporan Keuangan';
    protected static ?int $navigationSort = 6;

    public ?string $period1 = null;
    public ?string $period2 = null;
    public bool $loaded = false;

    // Results
    public array $labaRugi1 = [];
    public array $labaRugi2 = [];
    public array $neraca1 = [];
    public array $neraca2 = [];

    public function mount(): void
    {
        $this->period1 = now()->subMonth()->format('Y-m');
        $this->period2 = now()->format('Y-m');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\Select::make('period1')
                        ->label('Periode 1')
                        ->options($this->getPeriodOptions())
                        ->required()
                        ->default(now()->subMonth()->format('Y-m')),
                    Forms\Components\Select::make('period2')
                        ->label('Periode 2')
                        ->options($this->getPeriodOptions())
                        ->required()
                        ->default(now()->format('Y-m')),
                    Forms\Components\Placeholder::make('')
                        ->label(' ')
                        ->content(fn () => \Filament\Forms\Components\Actions::make([
                            \Filament\Forms\Components\Actions\Action::make('compare')
                                ->label('📊 Bandingkan')
                                ->color('primary')
                                ->action(fn () => $this->loadData()),
                        ])),
                ]),
            ])
            ->statePath('data');
    }

    protected function getPeriodOptions(): array
    {
        $options = [];
        for ($i = 0; $i < 24; $i++) {
            $date = now()->subMonths($i);
            $options[$date->format('Y-m')] = $date->format('F Y');
        }
        return $options;
    }

    public function loadData(): void
    {
        $p1 = $this->period1;
        $p2 = $this->period2;

        if (!$p1 || !$p2) return;

        $year1 = (int) substr($p1, 0, 4);
        $month1 = (int) substr($p1, 5, 2);
        $year2 = (int) substr($p2, 0, 4);
        $month2 = (int) substr($p2, 5, 2);

        // Laba/Rugi comparison
        $this->labaRugi1 = $this->getLabaRugi($year1, $month1);
        $this->labaRugi2 = $this->getLabaRugi($year2, $month2);

        // Neraca comparison
        $this->neraca1 = $this->getNeraca($year1, $month1);
        $this->neraca2 = $this->getNeraca($year2, $month2);

        $this->loaded = true;
    }

    protected function getLabaRugi(int $year, int $month): array
    {
        $pendapatan = [];
        $beban = [];
        $totalPendapatan = 0;
        $totalBeban = 0;

        // Revenue accounts
        $revenueAccounts = Account::where('type', 'revenue')->where('is_active', true)->get();
        foreach ($revenueAccounts as $account) {
            $balance = AutoJournalService::getAccountBalance($account->code, null, $year, $month);
            if (abs($balance) > 0.01) {
                $pendapatan[] = ['kode' => $account->code, 'nama' => $account->name, 'jumlah' => $balance];
                $totalPendapatan += $balance;
            }
        }

        // Expense accounts
        $expenseAccounts = Account::where('type', 'expense')->where('is_active', true)->get();
        foreach ($expenseAccounts as $account) {
            $balance = AutoJournalService::getAccountBalance($account->code, null, $year, $month);
            if (abs($balance) > 0.01) {
                $beban[] = ['kode' => $account->code, 'nama' => $account->name, 'jumlah' => $balance];
                $totalBeban += $balance;
            }
        }

        return [
            'pendapatan' => $pendapatan,
            'beban' => $beban,
            'total_pendapatan' => $totalPendapatan,
            'total_beban' => $totalBeban,
            'laba_bersih' => $totalPendapatan - $totalBeban,
        ];
    }

    protected function getNeraca(int $year, int $month): array
    {
        $aset = [];
        $kewajiban = [];
        $ekuitas = [];
        $totalAset = 0;
        $totalKewajiban = 0;
        $totalEkuitas = 0;

        $accounts = Account::where('is_active', true)->whereIn('type', ['asset', 'liability', 'equity'])->get();
        foreach ($accounts as $account) {
            $balance = AutoJournalService::getAccountBalance($account->code, null, $year, $month);
            if (abs($balance) > 0.01) {
                $item = ['kode' => $account->code, 'nama' => $account->name, 'jumlah' => $balance];
                if ($account->type === 'asset') {
                    $aset[] = $item;
                    $totalAset += $balance;
                } elseif ($account->type === 'liability') {
                    $kewajiban[] = $item;
                    $totalKewajiban += $balance;
                } elseif ($account->type === 'equity') {
                    $ekuitas[] = $item;
                    $totalEkuitas += $balance;
                }
            }
        }

        return [
            'aset' => $aset,
            'kewajiban' => $kewajiban,
            'ekuitas' => $ekuitas,
            'total_aset' => $totalAset,
            'total_kewajiban' => $totalKewajiban,
            'total_ekuitas' => $totalEkuitas,
        ];
    }
}
