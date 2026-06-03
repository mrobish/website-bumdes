<?php

namespace App\Filament\Admin\Resources\FinancialTemplateResource\Pages;

use App\Filament\Admin\Resources\FinancialTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinancialTemplate extends EditRecord
{
    protected static string $resource = FinancialTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
