<?php

namespace App\Filament\Client\Resources\FundsResource\Pages;

use App\Filament\Client\Resources\FundsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFunds extends ListRecords
{
    protected static string $resource = FundsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Add')
                ->icon('heroicon-o-plus'),
        ];
    }
}
