<?php

namespace App\Filament\Client\Resources\BiometricResource\Pages;

use App\Facades\Email;
use App\Filament\Client\Resources\BiometricResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Actions\Action;

class CreateBiometric extends CreateRecord
{
    protected static string $resource = BiometricResource::class;
    protected static bool $canCreateAnother = false;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        if ($this->record->status === 'approved') {
            \App\Models\Attendance::create([
                'user_id' => $this->record->user_id,
                'finger' => $this->record->timedate,
                'note' => 'biometric request',
            ]);
        }
    }
    protected function getCreatedNotification(): ?Notification
    {
        $users = Filament::getTenant()->users()->where('active', 1)->where('id', '!=', $this->record->user_id)
            ->whereHas('roles')
            ->where(function ($query) {
                $query->permission('biometric.approve')
                    ->orWhereHas('roles', function ($q) {
                        $q->where('name', 'Admin');
                    });
            })
            ->get();
        foreach ($users as $user) {
            Notification::make()
                ->title('Biometric Request')
                ->success()
                ->body('Biometric request has been created by ' . $this->record->user->name . '.')
                ->actions([
                    Action::make('view')
                        ->url($this->getResource()::getUrl()),
                    Action::make('mark-as-read')
                        ->markAsRead(),
                ])
                ->sendToDatabase($user, isEventDispatched: true);

            Email::mail('biometric.createRequest', $user->email, [
                'name' => $user->name,
                'requester_name' => $this->record->user->name,
                'timing' => $this->record->timedate,
                'company_name' => Filament::getTenant()->name,
                'cta_url' => url($this->getResource()::getUrl()),
            ]);
        }
        return Notification::make()
            ->success()
            ->title('Biometric Request')
            ->body('The biometric request has been created successfully.');
    }
}
