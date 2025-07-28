<?php

namespace App\Filament\Client\Resources\TodoResource\Pages;

use App\Filament\Client\Resources\TodoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTodo extends CreateRecord
{
    protected static string $resource = TodoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
