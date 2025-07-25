<?php

namespace App\Filament\Client\Resources\OffCyclePayRunResource\Pages;

use App\Filament\Client\Resources\OffCyclePayrollRecordResource;
use App\Filament\Client\Resources\OffCyclePayRunResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Carbon; // Make sure Carbon is imported


class ViewOffCyclePayRun extends ViewRecord
{
    protected static string $resource = OffCyclePayrollRecordResource::class;

    public function getTitle(): string
    {
        $record = $this->getRecord();
        return 'One-Time Pay Run - ' . Carbon::createFromDate($record->year, $record->month, 1)->format('F Y');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(), // You might keep an edit action for the pay run itself
            Actions\Action::make('back')
                ->label('Back to List')
                ->url(fn() => $this->getResource()::getUrl('index'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray'),
        ];
    }
}