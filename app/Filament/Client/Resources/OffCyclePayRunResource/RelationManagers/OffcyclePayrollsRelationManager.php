<?php

namespace App\Filament\Client\Resources\OffCyclePayRunResource\RelationManagers;

use App\Models\OffCyclePayroll;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Models\CompanyDetail;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class OffCyclePayrollsRelationManager extends RelationManager
{
    protected static string $relationship = 'offCyclePayrolls'; // The relationship method on OffCyclePayRun model
    protected static ?string $title = 'One-Time Payroll Records'; // Title for the tab/section

    // Replicate currency helpers if needed for this table
    protected static function getCurrencySymbolForAdmin(int $adminId): string
    {
        $country = Filament::getTenant()->country_id;
        return $country
            ? DB::table('tax_slabs')->where('country_id', $country)->value('salary_currency') ?? ''
            : '';
    }

    protected static function formatCurrency(string $symbol, float|int $amount): string
    {
        return $symbol . ' ' . number_format(round($amount));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Employee')->searchable(),
                Tables\Columns\TextColumn::make('total_earnings')
                    ->label('Earnings')
                    ->getStateUsing(fn(OffCyclePayroll $record) => static::formatCurrency(static::getCurrencySymbolForAdmin($record->team_id), $record->total_earnings)),
                Tables\Columns\TextColumn::make('total_deductions')
                    ->label('Tax')
                    ->getStateUsing(fn(OffCyclePayroll $record) => static::formatCurrency(static::getCurrencySymbolForAdmin($record->team_id), $record->total_deductions)),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending_approval' => 'warning',
                        'rejected' => 'danger',
                        'approved' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending_approval' => 'Pending Approval',
                        'rejected' => 'Rejected',
                        'approved' => 'Approved',
                        default => ucfirst($state),
                    })
                    ->description(fn(OffCyclePayroll $record): ?string => $record->status === 'rejected' ? $record->rejection_reason : null),
            ])
            ->searchPlaceholder('Search Employee')
            ->filters([
                // Filters for individual payrolls
            ])
            ->headerActions([
                // Actions for the relation manager (e.g., creating a new off-cycle payroll within this run, if applicable)
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make()
                //     ->visible(fn(OffCyclePayroll $record) => $record->status === 'pending_approval'),
                // Tables\Actions\ActionGroup::make([
                //     Tables\Actions\Action::make('approve_payroll')
                //         ->label('Approve')
                //         ->icon('heroicon-o-check-circle')
                //         ->color('success')
                //         ->visible(fn(OffCyclePayroll $record): bool => $record->status === 'pending_approval' && $this->getOwnerRecord()->status === 'pending_approval')
                //         ->requiresConfirmation()
                //         ->action(function (OffCyclePayroll $record) {
                //             $record->update(['status' => 'approved']);
                //             Notification::make()->title('One-Time Payroll Approved')->success()->send();
                //         }),
                //     Tables\Actions\Action::make('reject_payroll')
                //         ->label('Reject')
                //         ->icon('heroicon-o-x-circle')
                //         ->color('danger')
                //         ->visible(fn(OffCyclePayroll $record): bool => $record->status === 'pending_approval' && $this->getOwnerRecord()->status === 'pending_approval')
                //         ->form([
                //             Forms\Components\Textarea::make('rejection_reason')
                //                 ->label('Reason for Rejection')
                //                 ->required()
                //                 ->maxLength(255),
                //         ])
                //         ->requiresConfirmation()
                //         ->action(function (OffCyclePayroll $record, array $data) {
                //             $record->update([
                //                 'status' => 'rejected',
                //                 'rejection_reason' => $data['rejection_reason'],
                //             ]);
                //             Notification::make()->title('One-Time Payroll Rejected')->danger()->send();
                //         }),
                // ]),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('period_start', 'desc');
    }
}
