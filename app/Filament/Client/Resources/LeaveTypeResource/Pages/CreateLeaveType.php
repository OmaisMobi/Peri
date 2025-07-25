<?php

namespace App\Filament\Client\Resources\LeaveTypeResource\Pages;

use App\Filament\Client\Resources\LeaveTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLeaveType extends CreateRecord
{
    protected static string $resource = LeaveTypeResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
