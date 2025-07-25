<?php

namespace App\Filament\Client\Resources\PayRunResource\Pages;

use App\Filament\Client\Resources\PayRunResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditPayRun extends EditRecord
{
    protected static string $resource = PayRunResource::class;

    public function getTitle(): string
    {
        if ($this->record) {
            $monthName = Carbon::createFromDate($this->record->year, $this->record->month, 1)->format('F');
            return $monthName . " " . $this->record->year;
        }

        return 'Manage Payroll';
    }

    protected function getHeaderActions(): array
    {
        $user = Auth::user();
        return [
            Actions\Action::make('back')
                ->label('Back')
                ->url(fn() => $this->getResource()::getUrl())
                ->icon('heroicon-o-arrow-left')
                ->color('gray'),

            ...parent::getHeaderActions(),

            Actions\Action::make('submit_for_approval')
                ->label('Submit')
                ->color('primary')
                ->action('submitPayRun')
                ->visible(fn($record) => $record->status === 'draft' || $record->status === 'rejected')
                ->requiresConfirmation(false),

            Actions\Action::make('approve_pay_run')
                ->label('Approve')
                ->color('success')
                ->action('approvePayRun')
                ->visible(
                    fn($record) => ($record->status === 'pending_approval') &&
                        ($user->hasPermissionTo('payroll.approve') || $user->hasRole('Admin'))
                )
                ->requiresConfirmation(false),

            Actions\Action::make('reject_pay_run')
                ->label('Reject')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Textarea::make('rejection_reason')
                        ->label('Reason for Rejection')
                        ->required()
                        ->rows(2),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'status' => 'rejected',
                        'rejection_reason' => $data['rejection_reason'] ?? null,
                    ]);

                    Notification::make()
                        ->warning()
                        ->title('Payroll Rejected')
                        ->body('Reason: ' . ($data['rejection_reason'] ?? '-'))
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(
                    fn($record) => ($record->status === 'pending_approval') &&
                        ($user->hasPermissionTo('payroll.approve') || $user->hasRole('Admin'))
                )
                ->modalHeading('')
                ->modalSubmitActionLabel('Reject'),

            Actions\Action::make('delete')
                ->color('danger')
                ->modalHeading('Delete Pay Run')
                ->action('deletePayRun')
                ->successNotificationTitle('Pay Run Deleted')
                ->visible(fn($record) => in_array($record->status, ['draft', 'pending_approval', 'rejected'])),

        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    public function submitPayRun(): void
    {
        $this->record->update(['status' => 'pending_approval']);
        Notification::make()->success()->title('Payroll Submitted')->body('Payroll has been submitted for approval.')->send();
        $this->redirect($this->getResource()::getUrl('index'));
    }

    public function approvePayRun(): void
    {
        DB::beginTransaction();
        try {
            $this->record->payrolls()->update(['status' => true]);
            $this->record->update(['status' => 'finalized']);
            $this->redirect($this->getResource()::getUrl('index'));

            DB::commit();
            Notification::make()->success()->title('Payroll Finalized')->body('Associated payroll has been approved.')->send();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()->danger()->title('Error Finalizing Payroll')->body($e->getMessage())->send();
        }
    }

    public function rejectPayRun(): void
    {
        $data = $this->form->getState();
        $this->record->update([
            'status' => 'rejected',
            'rejection_reason' => $data['rejection_reason'] ?? null,
        ]);

        Notification::make()
            ->warning()
            ->title('Payroll Rejected')
            ->body('Reason: ' . ($data['rejection_reason'] ?? '-'))
            ->send();

        $this->redirect($this->getResource()::getUrl('index'));
    }

    public function deletePayRun(): void
    {
        DB::beginTransaction();

        try {
            $this->record->payrolls()->delete();
            $this->record->delete();

            DB::commit();

            Notification::make()
                ->success()
                ->title('Pay Run Deleted')
                ->body('The pay run and its related payrolls have been deleted.')
                ->send();

            $this->redirect($this->getResource()::getUrl('index'));
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->danger()
                ->title('Delete Failed')
                ->body('An error occurred: ' . $e->getMessage())
                ->send();
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->listeners = array_merge($this->listeners, ['updatePayrollsRepeater' => '$refresh']);
    }
}
