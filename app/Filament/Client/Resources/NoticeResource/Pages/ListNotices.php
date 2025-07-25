<?php

namespace App\Filament\Client\Resources\NoticeResource\Pages;

use App\Filament\Client\Resources\NoticeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNotices extends ListRecords
{
    protected static string $resource = NoticeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add')
                ->icon('heroicon-o-plus'),
        ];
    }
}
