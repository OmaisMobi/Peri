<?php

namespace App\Filament\Client\Resources\PayRunResource\RelationManagers;

use App\Filament\Client\Resources\PayRunResource;
use App\Filament\Client\Resources\PayRunResource\Pages\EditPayroll;
use App\Models\Payroll;
use App\Services\PayrollCalculationService;
use App\Models\SalaryComponent;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class PayrollsRelationManager extends RelationManager
{
    protected static string $relationship = 'payrolls';
    protected static ?string $recordTitleAttribute = 'user.name';

    public function table(Table $table): Table
    {
        $adminId = $this->getOwnerRecord()->team_id;
        $payrollService = app(PayrollCalculationService::class);
        $currency = $payrollService->getCurrencySymbolForAdmin($adminId);

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Employee')->searchable(),
                Tables\Columns\TextColumn::make('base_salary')->label('Base Salary')->formatStateUsing(fn($state) => $currency . ' ' . number_format($state ?? 0)),
                Tables\Columns\TextColumn::make('total_earnings')->label('Earnings')->formatStateUsing(fn($state) => $currency . ' ' . number_format($state ?? 0)),
                Tables\Columns\TextColumn::make('total_deductions')->label('Deductions')->formatStateUsing(fn($state) => $currency . ' ' . number_format($state ?? 0)),
                Tables\Columns\TextColumn::make('tax_data.monthly_tax_calculated')->label('Tax')->formatStateUsing(fn($state) => $currency . ' ' . number_format($state ?? 0)),
                Tables\Columns\TextColumn::make('net_payable_salary')->label('Net Pay')->formatStateUsing(fn($state) => $currency . ' ' . number_format($state ?? 0)),
            ])
            ->searchPlaceholder('Search Employee')
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn(Payroll $record) => PayRunResource::getUrl('edit-payroll', [
                        'payrun' => $record->pay_run_id,
                        'record' => $record->id,
                    ]))
                    ->visible(fn(): bool => $this->getOwnerRecord()->status === 'draft' || $this->getOwnerRecord()->status === 'rejected'),
                Tables\Actions\DeleteAction::make()
                    ->label('Skip')
                    ->tooltip('Skip employee from this payroll')
                    ->icon('heroicon-c-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Skip employee from this payroll?')
                    ->modalSubheading('This action will delete the payroll record and cannot be undone.')
                    ->modalButton('Yes, Skip')
                    ->visible(fn(): bool => !in_array($this->getOwnerRecord()->status, ['pending_approval', 'finalized'])),

            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulkSkipPayroll')
                    ->label('Skip Selected')
                    ->icon('heroicon-c-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Skip selected employee(s) from payroll?')
                    ->modalSubheading('This will permanently delete their payroll records from this pay run. This action cannot be undone.')
                    ->modalButton('Yes, Skip')
                    ->action(function ($records) {
                        $skippedCount = 0;
                        foreach ($records as $record) {
                            if (!in_array($record->payRun->status ?? null, ['pending_approval', 'finalized'])) {
                                $record->delete();
                                $skippedCount++;
                            }
                        }
                        Notification::make()->success()->title('Skipped Employees')->body("Successfully skipped {$skippedCount} employee(s) from this payroll.")->send();
                    })
                    ->deselectRecordsAfterCompletion()
                    ->visible(fn(): bool => !in_array($this->getOwnerRecord()->status, ['pending_approval', 'finalized'])),
            ]);
    }
}
