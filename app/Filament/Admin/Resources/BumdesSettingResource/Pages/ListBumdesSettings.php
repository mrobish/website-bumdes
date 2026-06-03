<?php

namespace App\Filament\Admin\Resources\BumdesSettingResource\Pages;

use App\Filament\Admin\Resources\BumdesSettingResource;
use App\Models\BumdesSetting;
use Filament\Resources\Pages\ListRecords;

class ListBumdesSettings extends ListRecords
{
    protected static string $resource = BumdesSettingResource::class;

    protected function getRedirectUrl(): string
    {
        $settings = BumdesSetting::getSettings();
        return $this->getResource()::getUrl('edit', ['record' => $settings]);
    }

    protected function mounted(): void
    {
        // Auto redirect to edit page
        $settings = BumdesSetting::getSettings();
        redirect()->to($this->getResource()::getUrl('edit', ['record' => $settings]));
    }
}
