<?php

namespace App\Filament\Client\Resources\SalaryComponentResource\Pages;

use App\Filament\Client\Resources\SalaryComponentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSalaryComponent extends CreateRecord
{
    protected static string $resource = SalaryComponentResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
