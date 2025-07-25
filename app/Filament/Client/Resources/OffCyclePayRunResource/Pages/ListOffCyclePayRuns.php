<?php

namespace App\Filament\Client\Resources\OffCyclePayRunResource\Pages;

use App\Filament\Client\Resources\OffCyclePayRunResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOffCyclePayRuns extends ListRecords
{
    protected static string $resource = OffCyclePayRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
