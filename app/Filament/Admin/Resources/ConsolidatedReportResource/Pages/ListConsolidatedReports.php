<?php

namespace App\Filament\Admin\Resources\ConsolidatedReportResource\Pages;

use App\Filament\Admin\Resources\ConsolidatedReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsolidatedReports extends ListRecords
{
    protected static string $resource = ConsolidatedReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
