<?php

namespace App\Filament\Resources\SaaSFeatureResource\Pages;

use App\Filament\Resources\SaaSFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSaaSFeatures extends ListRecords
{
    protected static string $resource = SaaSFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
