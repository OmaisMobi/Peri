<?php

namespace App\Filament\Client\Resources\TodoResource\Pages;

use App\Filament\Client\Resources\TodoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTodos extends ListRecords
{
    protected static string $resource = TodoResource::class;
    protected static ?string $title = 'To-Do List';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add')
                ->icon('heroicon-o-plus'),
        ];
    }
}
