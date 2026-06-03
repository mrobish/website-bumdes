<?php

namespace App\Filament\Admin\Resources\FinancialTransactionResource\Pages;

use App\Filament\Admin\Resources\FinancialTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinancialTransaction extends EditRecord
{
    protected static string $resource = FinancialTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
