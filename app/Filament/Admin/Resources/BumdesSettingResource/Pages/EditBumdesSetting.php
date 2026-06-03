<?php

namespace App\Filament\Admin\Resources\BumdesSettingResource\Pages;

use App\Filament\Admin\Resources\BumdesSettingResource;
use App\Models\BumdesSetting;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditBumdesSetting extends EditRecord
{
    protected static string $resource = BumdesSettingResource::class;

    public function getRecordTitle(): Htmlable|string
    {
        return 'Identitas BUMDes';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Always get or create the settings record
        $settings = BumdesSetting::getSettings();
        $data = $settings->toArray();
        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $settings = BumdesSetting::getSettings();
        $settings->update($data);
        return $settings;
    }

    protected function afterSave(): void
    {
        // Clear config cache after saving settings
        try {
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
        } catch (\Exception $e) {
            // Ignore cache errors
        }
    }
}
