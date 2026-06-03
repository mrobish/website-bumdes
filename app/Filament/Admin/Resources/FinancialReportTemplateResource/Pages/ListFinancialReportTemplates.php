<?php

namespace App\Filament\Admin\Resources\FinancialReportTemplateResource\Pages;

use App\Filament\Admin\Resources\FinancialReportTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinancialReportTemplates extends ListRecords
{
    protected static string $resource = FinancialReportTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
