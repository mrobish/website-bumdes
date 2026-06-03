<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Get counts from database
        $units = DB::table('bumdes_units')->count();
        $products = DB::table('umkm_products')->count();
        $orders = DB::table('orders')->count();
        $contacts = DB::table('contacts')->count();

        return [
            Stat::make('Unit Usaha', $units)
                ->description('Unit usaha terdaftar')
                ->descriptionIcon('heroicon-o-building-office')
                ->color('success'),
            
            Stat::make('Produk UMKM', $products)
                ->description('Produk terdaftar')
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->color('warning'),
            
            Stat::make('Total Pesanan', $orders)
                ->description('Pesanan masuk')
                ->descriptionIcon('heroicon-o-cart')
                ->color('danger'),
            
            Stat::make('Pesan Masuk', $contacts)
                ->description('Aspirasi & pengaduan')
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color('info'),
        ];
    }
}
