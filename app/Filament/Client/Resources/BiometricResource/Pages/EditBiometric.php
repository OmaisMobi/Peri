<?php

namespace App\Filament\Client\Resources\BiometricResource\Pages;

use App\Facades\Email;
use App\Filament\Client\Resources\BiometricResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Actions\Action;
use Illuminate\Support\Facades\Auth;

class EditBiometric extends EditRecord
{
    protected static string $resource = BiometricResource::class;

    public function getTitle(): string
    {
        return $this->record->user->name . ' - Biometric Request';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function afterSave(): void
    {
        if ($this->record->wasChanged('status') && $this->record->status === 'approved') {
            \App\Models\Attendance::create([
                'user_id' => $this->record->user_id,
                'finger' => $this->record->timedate,
                'note' => 'biometric request',
                'team_id' => Filament::getTenant()->id,
            ]);
            Notification::make()
                ->title('Biometric Request')
                ->success()
                ->body('Biometric request has been approved by ' . Auth::user()->name . '.')
                ->actions([
                    Action::make('view')
                        ->url($this->getResource()::getUrl()),
                    Action::make('mark-as-read')
                        ->markAsRead(),
                ])
                ->sendToDatabase($this->record->user, isEventDispatched: true);

            Email::mail('biometric.approve', $this->record->user->email, [
                'name' => $this->record->user->name,
                'approver_name' => Auth::user()->name,
                'timing' => $this->record->timedate,
                'company_name' => Filament::getTenant()->name,
                'cta_url' => url($this->getResource()::getUrl()),
            ]);
        }
    }
}
