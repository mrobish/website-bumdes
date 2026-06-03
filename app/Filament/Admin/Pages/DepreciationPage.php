<?php

namespace App\Filament\Admin\Pages;

use App\Models\Asset;
use App\Models\AssetDepreciation;
use Filament\Forms;
use Filament\Pages\Page;

class DepreciationPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Hitung Penyusutan';
    protected static ?string $title = 'Penyusutan Aset Otomatis';
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.admin.pages.depreciation-page';

    public $period = '';
    public $results = [];
    public $totalDepreciation = 0;

    public function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Hitung Penyusutan')
                ->schema([
                    Forms\Components\TextInput::make('period')
                        ->label('Periode (YYYY-MM)')
                        ->placeholder(date('Y-m'))
                        ->default(date('Y-m'))
                        ->required(),
                    Forms\Components\Button::make('hitung')
                        ->label('Hitung Penyusutan')
                        ->color('primary')
                        ->action('calculateDepreciation'),
                ]),
        ];
    }

    public function calculateDepreciation(): void
    {
        $data = $this->form->getState();
        $period = $data['period'] ?? date('Y-m');

        $this->results = AssetDepreciation::generateForPeriod($period);
        $this->totalDepreciation = collect($this->results)->sum('depreciation');
        $this->period = $period;

        if (empty($this->results)) {
            session()->flash('info', 'Tidak ada penyusutan untuk periode ' . $period . '. Semua aset sudah selesai disusutkan atau belum ada data aset.');
        } else {
            session()->flash('success', count($this->results) . ' aset berhasil dihitung penyusutannya.');
        }
    }
}
