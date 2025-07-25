<?php

namespace App\Filament\Client\Resources\OffCyclePayrollRecordResource\Pages;

use App\Filament\Client\Resources\OffCyclePayrollRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOffCyclePayrollRecords extends ListRecords
{
    protected static string $resource = OffCyclePayrollRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
