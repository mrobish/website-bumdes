<?php

namespace App\Filament\Admin\Resources\HutangResource\Pages;

use App\Filament\Admin\Resources\HutangResource;
use App\Services\HutangService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateHutang extends CreateRecord
{
    protected static string $resource = HutangResource::class;

    protected function handleRecordCreation(array $data): \App\Models\Hutang
    {
        return HutangService::create($data);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return '✅ Hutang tersimpan & jurnal double-entry dibuat!';
    }
}
