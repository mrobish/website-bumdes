<?php

namespace App\Filament\Admin\Resources\FinancialUploadResource\Pages;

use App\Filament\Admin\Resources\FinancialUploadResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFinancialUpload extends CreateRecord
{
    protected static string $resource = FinancialUploadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        $file = $this->data['file_path'] ?? null;
        if ($file) {
            $data['file_name'] = basename($file);
            $data['file_size'] = Storage::size($file);
        }

        return $data;
    }
}
