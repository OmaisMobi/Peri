<?php

namespace App\Filament\Resources\TaxSlabsResource\Pages;

use App\Filament\Resources\TaxSlabsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTaxSlabs extends ListRecords
{
    protected static string $resource = TaxSlabsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Add')
                ->icon('heroicon-o-plus'),
        ];
    }
}
