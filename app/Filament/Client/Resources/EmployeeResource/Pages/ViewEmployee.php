<?php

namespace App\Filament\Client\Resources\EmployeeResource\Pages;

use App\Filament\Client\Resources\EmployeeResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected static string $view = 'filament.client.resources.employee-resource.pages.view-employee-custom';

    public function getTitle(): string
    {
        return "Employee Profile";
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->url(fn() => $this->getResource()::getUrl())
                ->icon('heroicon-o-arrow-left')
                ->color('gray'),
        ];
    }
}
