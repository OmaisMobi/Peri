<?php

namespace App\Filament\Client\Resources\FundsResource\Pages;

use App\Filament\Client\Resources\FundsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFunds extends EditRecord
{
    protected static string $resource = FundsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
