<?php

namespace App\Livewire;

use Filament\Facades\Filament;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Request;

class RemoteAttendance extends Component
{
    public function render()
    {
        // Check if user has offsite or hybrid attendance type
        $user = Auth::user();
        $hasRemoteAccess = $user && in_array($user->attendance_type, ['offsite', 'hybrid']);

        // Always return a view, but pass the permission status
        return view('livewire.remote-attendance', [
            'showComponent' => $hasRemoteAccess
        ]);
    }

    public function recordAttendance()
    {
        $user = Auth::user();

        // Double-check permission before recording attendance
        if (!in_array($user->attendance_type, ['offsite', 'hybrid'])) {
            Notification::make()
                ->title('Permission Denied')
                ->body('You do not have permission to record remote attendance.')
                ->danger()
                ->send();
            return;
        }

        $timezone = Filament::getTenant()->timezone ?? 'UTC';
        DB::table('attendances')->insert([
            'user_id'    => $user->id,
            'finger'     => now()->setTimezone($timezone),
            'note'       => 'Recorded via dashboard button',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Notification::make()
            ->title('Attendance Recorded')
            ->body('🕒 Recorded at ' . now()->setTimezone($timezone)->format('h:i A'))
            ->success()
            ->send();
    }

    public static function shouldRender(): bool
    {
        $user = Auth::user();
        return $user && in_array($user->attendance_type, ['offsite', 'hybrid']);
    }
}
