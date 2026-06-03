<?php
namespace App\Filament\Admin\Resources\CapitalContributionResource\Pages;
use App\Filament\Admin\Resources\CapitalContributionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListCapitalContributions extends ListRecords { protected static string $resource = CapitalContributionResource::class; protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; } }
