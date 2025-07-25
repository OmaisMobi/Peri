<?php

namespace App\Filament\Client\Resources\PayRunResource\Pages;

use App\Filament\Client\Pages\OffcyclePayrollPage;
use App\Filament\Client\Resources\PayRunResource;
use App\Models\PayRun;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;

class ListPayRuns extends ListRecords
{
    protected static string $resource = PayRunResource::class;

    protected static string $view = 'filament.client.pages.payroll-tabs';

    public ?string $activeTab = 'on_cycle';

    protected function getHeaderActions(): array
    {
        $hasActivePayRun = PayRun::where('team_id', Filament::getTenant()->id)
            ->whereIn('status', ['draft', 'pending_approval', 'rejected'])
            ->exists();

        return [
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->use(\App\Imports\PayrunWithPayrollImport::class)
                ->color("primary"),
            ActionGroup::make([
                Actions\Action::make('regular_payroll')
                    ->label('Regular Payroll')
                    ->url(PayRunResource::getUrl('create'))
                    ->icon(null)
                    ->visible(fn() => !$hasActivePayRun),

                Actions\Action::make('one_time_payment')
                    ->label('One-Time Payment')
                    ->url(OffcyclePayrollPage::getUrl())
                    ->icon(null),
                Actions\Action::make('fund_reburst')
                    ->label('Fund Reimbursement')
                    ->url(PayRunResource::getUrl('fund-reburst'))
                    ->icon(null),
            ])
                ->label('Create Pay Run')
                ->color('primary')
                ->icon('')
                ->button(),
        ];
    }

    public function getTableQuery(): Builder
    {
        return parent::getTableQuery()->where('paid', false);
    }

    // Livewire method to change the active tab
    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }
}
