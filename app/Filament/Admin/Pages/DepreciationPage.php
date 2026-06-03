<?php

namespace App\Filament\Admin\Pages;

use App\Models\Asset;
use App\Services\DepreciationService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class DepreciationPage extends Page
{
    protected static string $view = 'filament.pages.depreciation';
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = '💰 Keuangan';
    protected static ?string $navigationLabel = 'Depresiasi';
    protected static ?string $title = 'Depresiasi Aset';
    protected static ?int $navigationSort = 5;

    public int $year = 0;
    public int $month = 0;
    public bool $loaded = false;

    public array $assets = [];
    public float $totalDepreciation = 0;

    public function mount(): void
    {
        $this->year = (int) now()->year;
        $this->month = (int) now()->month;
    }

    public function loadData(): void
    {
        $this->assets = Asset::where('status', 'active')
            ->with('businessUnit')
            ->get()
            ->map(fn ($asset) => [
                'name' => $asset->name,
                'unit' => $asset->unit->name ?? '-',
                'purchase_price' => $asset->purchase_price,
                'useful_life' => $asset->useful_life_months,
                'accumulated' => $asset->accumulated_depreciation,
                'book_value' => $asset->current_book_value,
                'monthly' => ($asset->purchase_price - $asset->salvage_value) / max($asset->useful_life_months, 1),
            ])
            ->toArray();

        $this->totalDepreciation = collect($this->assets)->sum('monthly');
        $this->loaded = true;
    }

    public function runDepreciation(): void
    {
        $result = DepreciationService::runMonthlyDepreciation($this->year, $this->month);

        Notification::make()
            ->title('✅ Depresiasi Selesai!')
            ->body("{$result['processed']} aset diproses. Total: Rp " . number_format($result['total_depreciation'], 0, ',', '.'))
            ->success()
            ->send();

        $this->loadData();
    }
}
