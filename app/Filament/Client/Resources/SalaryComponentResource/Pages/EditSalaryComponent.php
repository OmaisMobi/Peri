<?php

namespace App\Filament\Client\Resources\SalaryComponentResource\Pages;

use App\Filament\Client\Resources\SalaryComponentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalaryComponent extends EditRecord
{
    protected static string $resource = SalaryComponentResource::class;

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
