<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AboutPage extends Page
{
    protected static string $view = 'filament.pages.about';
    protected static ?string $navigationIcon = 'heroicon-o-information-circle';
    protected static ?string $navigationGroup = '⚙️ Pengaturan';
    protected static ?string $navigationLabel = 'Tentang';
    protected static ?string $title = 'Tentang Aplikasi';
    protected static ?int $navigationSort = 99;

    public function getTitle(): string
    {
        return 'Tentang Aplikasi';
    }
}
