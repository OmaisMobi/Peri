<?php

namespace App\Filament\Client\Resources\SalaryComponentResource\Pages;

use App\Filament\Client\Resources\SalaryComponentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSalaryComponents extends ListRecords
{
    protected static string $resource = SalaryComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Add')
                ->icon('heroicon-o-plus'),
        ];
    }
}
