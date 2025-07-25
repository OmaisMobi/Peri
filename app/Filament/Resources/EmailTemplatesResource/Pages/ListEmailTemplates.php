<?php

namespace App\Filament\Resources\EmailTemplatesResource\Pages;

use App\Filament\Pages\SmtpConfig;
use App\Filament\Resources\EmailTemplatesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmailTemplates extends ListRecords
{
    protected static string $resource = EmailTemplatesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add')
                ->tooltip('Add')
                ->icon('heroicon-o-plus'),
            Actions\Action::make('smtp-config')
                ->url(SmtpConfig::getUrl())
                ->label('Smtp Config')
                ->tooltip('Smtp Config')
                ->icon('heroicon-o-cog')
                ->hiddenLabel(),
        ];
    }
}
