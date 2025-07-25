<?php

namespace App\Filament\Client\Resources\PayrollRecordResource\Pages;

use App\Filament\Client\Resources\PayrollRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayrollRecords extends ListRecords
{
    protected static string $resource = PayrollRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PayrollRecordResource::getGeneratePayrollHeaderAction(),
            PayrollRecordResource::getDownloadAllPdfsForPeriodHeaderAction(),
        ];
    }
}
