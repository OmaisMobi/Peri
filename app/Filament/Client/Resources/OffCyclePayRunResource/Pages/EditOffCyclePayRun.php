<?php

namespace App\Filament\Client\Resources\OffCyclePayRunResource\Pages;

use App\Filament\Client\Resources\OffCyclePayRunResource;
use App\Filament\Client\Resources\PayRunResource;
use App\Models\OffCyclePayRun;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditOffCyclePayRun extends EditRecord
{
    protected static string $resource = OffCyclePayRunResource::class;

    // Optional: Customize the title
    public function getTitle(): string
    {
        return 'Due Date: '; // More descriptive title for the parent record
    }

    protected function getHeaderActions(): array
    {
        $user = Auth::user();
        $record = $this->record; // This is an App\Models\OffCyclePayRun instance

        return [
            Actions\Action::make('back')
                ->label('Back')
                ->url(fn() => PayRunResource::getUrl('index')) // Link back to the main pay run list
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),

            Actions\Action::make('submit_for_approval')
                ->label('Submit for Approval')
                ->color('primary')
                ->action('submitOffCyclePayRunForApproval') // Custom method to handle submission
                ->visible(fn(OffCyclePayRun $record) => $record->status === 'draft' || $record->status === 'rejected'),

            Actions\Action::make('approve_off_cycle_pay_run')
                ->label('Approve')
                ->color('success')
                ->action('approveOffCyclePayRun') // Custom method to handle approval
                ->visible(
                    fn(OffCyclePayRun $record) => ($record->status === 'pending_approval') &&
                        ($user->hasPermissionTo('payroll.approve') || $user->hasRole('Admin'))
                ),

            Actions\Action::make('reject_off_cycle_pay_run')
                ->label('Reject')
                ->color('warning')
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Reason for Rejection')
                        ->required()
                        ->rows(2)
                        ->placeholder('Enter the reason for rejection.'),
                ])
                ->action(function (array $data) use ($record) {
                    $this->rejectOffCyclePayRun($data); // Call custom method for rejection
                })
                ->visible(
                    fn(OffCyclePayRun $record) => ($record->status === 'pending_approval') &&
                        ($user->hasPermissionTo('payroll.approve') || $user->hasRole('Admin'))
                )
                ->modalHeading('') // No specific heading for reject modal, uses default
                ->modalSubmitActionLabel('Reject'),

            Actions\DeleteAction::make()
                ->requiresConfirmation()
                // CORRECTED: Type hint is now OffCyclePayRun
                ->visible(fn(OffCyclePayRun $record) => !in_array($record->status, ['approved', 'finalized']))
                ->after(function () {
                    // Redirect back to the PayRunResource index after deletion
                    return redirect(PayRunResource::getUrl('index'));
                }),
        ];
    }

    // Custom method to handle submitting an OffCyclePayRun for approval
    public function submitOffCyclePayRunForApproval(): void
    {
        DB::beginTransaction();
        try {
            // Update the parent OffCyclePayRun status
            $this->record->update(['status' => 'pending_approval']);

            // Update all associated OffCyclePayroll records to pending_approval as well
            $this->record->offCyclePayrolls()->update(['status' => 'pending_approval']);

            DB::commit();
            Notification::make()
                ->success()
                ->title('One-Time Payment Batch Submitted')
                ->body('The one-time payment batch has been submitted for approval.')
                ->send();
            $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record->id])); // Redirect back to this page to refresh actions
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Could not submit one-time payment batch: ' . $e->getMessage())
                ->send();
        }
    }

    // Custom method to handle approving an OffCyclePayRun
    public function approveOffCyclePayRun(): void
    {
        DB::beginTransaction();
        try {
            // Update the parent OffCyclePayRun status
            $this->record->update(['status' => 'approved']);

            // Update all associated OffCyclePayroll records to approved
            $this->record->offCyclePayrolls()->update(['status' => 'approved']);

            DB::commit();
            Notification::make()
                ->success()
                ->title('One-Time Payment Batch Approved')
                ->body('The one-time payment batch and associated records have been approved.')
                ->send();
            $this->redirect(PayRunResource::getUrl('index')); // Redirect to the main payroll list after approval
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->danger()
                ->title('Error Approving One-Time Payment Batch')
                ->body($e->getMessage())
                ->send();
        }
    }

    // Custom method to handle rejecting an OffCyclePayRun
    public function rejectOffCyclePayRun(array $data): void
    {
        DB::beginTransaction();
        try {
            // Update the parent OffCyclePayRun status and rejection reason
            $this->record->update([
                'status' => 'rejected',
                'rejection_reason' => $data['rejection_reason'] ?? null,
            ]);

            // Update all associated OffCyclePayroll records to rejected and set rejection reason
            $this->record->offCyclePayrolls()->update([
                'status' => 'rejected',
                'rejection_reason' => $data['rejection_reason'] ?? null,
            ]);

            DB::commit();
            Notification::make()
                ->warning()
                ->title('One-Time Payment Batch Rejected')
                ->body('Reason: ' . ($data['rejection_reason'] ?? '-'))
                ->send();
            $this->redirect(PayRunResource::getUrl('index')); // Redirect to the main payroll list after rejection
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->danger()
                ->title('Error Rejecting One-Time Payment Batch')
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [];
    }
}