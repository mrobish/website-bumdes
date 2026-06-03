<?php

namespace App\Filament\Admin\Pages;

use App\Services\FinancialAutoCalculateService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class FinanceDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = '💰 Keuangan';
    protected static ?string $navigationLabel = 'Dashboard Keuangan';
    protected static ?string $title = 'Dashboard Keuangan';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.finance-dashboard';

    public ?string $bulan = null;
    public array $data = [];

    public function mount(): void
    {
        $this->bulan = now()->format('Y-m');
        $this->loadData();
    }

    public function loadData(): void
    {
        $service = new FinancialAutoCalculateService();
        $this->data = $service->getDashboard($this->bulan);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('bulan')
                    ->label('Periode Bulan')
                    ->options($this->getBulanOptions())
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadData())
                    ->required(),
            ]);
    }

    protected function getBulanOptions(): array
    {
        $options = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $options[$date->format('Y-m')] = $date->format('F Y');
        }
        return $options;
    }

    public function getLabaRugi(): array
    {
        return $this->data['laba_rugi'] ?? [];
    }

    public function getNeraca(): array
    {
        return $this->data['neraca'] ?? [];
    }

    public function getArusKas(): array
    {
        return $this->data['arus_kas'] ?? [];
    }

    public function getBukuBesar(): array
    {
        return $this->data['buku_besar'] ?? [];
    }

    public function getPerubahanModal(): array
    {
        return $this->data['perubahan_modal'] ?? [];
    }

    public function getRealisasiAnggaran(): array
    {
        return $this->data['realisasi_anggaran'] ?? [];
    }

    public function getCAT(): array
    {
        return $this->data['cat'] ?? [];
    }
}
