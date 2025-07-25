<?php

namespace App\Filament\Client\Resources\BiometricResource\Pages;

use App\Filament\Client\Resources\BiometricResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBiometrics extends ListRecords
{
    protected static string $resource = BiometricResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add')
                ->icon('heroicon-o-plus'),
        ];
    }
}
