<?php

namespace App\Filament\Client\Resources\NoticeResource\Pages;

use App\Filament\Client\Resources\NoticeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNotice extends CreateRecord
{
    protected static string $resource = NoticeResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
