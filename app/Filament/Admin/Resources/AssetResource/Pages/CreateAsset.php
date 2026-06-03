<?php

namespace App\Filament\Admin\Resources\AssetResource\Pages;

use App\Filament\Admin\Resources\AssetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['depreciation_method'] = 'straight_line';
        $data['accumulated_depreciation'] = 0;
        $data['current_book_value'] = $data['purchase_price'];
        return $data;
    }
}
