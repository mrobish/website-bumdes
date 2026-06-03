<?php

namespace App\Filament\Admin\Resources\FinancialTransactionResource\Pages;

use App\Filament\Admin\Resources\FinancialTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFinancialTransaction extends CreateRecord
{
    protected static string $resource = FinancialTransactionResource::class;
}
