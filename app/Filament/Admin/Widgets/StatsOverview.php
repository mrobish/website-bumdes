<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $units = DB::table('business_units')->count();
        $products = DB::table('products')->count();
        $news = DB::table('news')->count();
        $users = DB::table('users')->where('status', 'active')->count();
        $transactions = DB::table('financial_transactions')->count();
        $gallery = DB::table('galleries')->count();

        return [
            Stat::make('Unit Usaha', $units)
                ->description('Unit usaha terdaftar')
                ->descriptionIcon('heroicon-o-building-office')
                ->color('success'),

            Stat::make('Produk', $products)
                ->description('Produk terdaftar')
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->color('warning'),

            Stat::make('Berita', $news)
                ->description('Artikel berita')
                ->descriptionIcon('heroicon-o-newspaper')
                ->color('info'),

            Stat::make('Transaksi', number_format($transactions))
                ->description('Transaksi keuangan')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('primary'),

            Stat::make('User Aktif', $users)
                ->description('User terdaftar')
                ->descriptionIcon('heroicon-o-users')
                ->color('danger'),

            Stat::make('Galeri', $gallery)
                ->description('Foto/video')
                ->descriptionIcon('heroicon-o-photo')
                ->color('gray'),
        ];
    }
}
