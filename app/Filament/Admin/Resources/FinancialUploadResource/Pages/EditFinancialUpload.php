<?php

namespace App\Filament\Admin\Resources\FinancialUploadResource\Pages;

use App\Filament\Admin\Resources\FinancialUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinancialUpload extends EditRecord
{
    protected static string $resource = FinancialUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
