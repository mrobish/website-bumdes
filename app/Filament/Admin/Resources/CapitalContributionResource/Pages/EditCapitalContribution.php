<?php
namespace App\Filament\Admin\Resources\CapitalContributionResource\Pages;
use App\Filament\Admin\Resources\CapitalContributionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditCapitalContribution extends EditRecord { protected static string $resource = CapitalContributionResource::class; protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; } }
