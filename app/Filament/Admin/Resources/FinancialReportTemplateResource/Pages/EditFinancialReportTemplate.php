<?php

namespace App\Filament\Admin\Resources\FinancialReportTemplateResource\Pages;

use App\Filament\Admin\Resources\FinancialReportTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinancialReportTemplate extends EditRecord
{
    protected static string $resource = FinancialReportTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
