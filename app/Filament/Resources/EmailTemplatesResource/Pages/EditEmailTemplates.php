<?php

namespace App\Filament\Resources\EmailTemplatesResource\Pages;

use App\Filament\Resources\EmailTemplatesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmailTemplates extends EditRecord
{
    protected static string $resource = EmailTemplatesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
