<?php

namespace App\Filament\Admin\Resources\FinancialUploadResource\Pages;

use App\Filament\Admin\Resources\FinancialUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinancialUploads extends ListRecords
{
    protected static string $resource = FinancialUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
