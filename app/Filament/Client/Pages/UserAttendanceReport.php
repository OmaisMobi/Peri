<?php

namespace App\Filament\Client\Pages;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Pages\Page;

class UserAttendanceReport extends Page
{
    protected static string $view = 'filament.client.pages.user-attendance-report';

    public function getTitle(): string
    {
        $userId = request()->get('record');
        $user = User::withoutGlobalScopes()->find($userId);

        if ($user) {
            return 'Attendance Report' . now()->format(' Y - ') . $user->name;
        } else {
            return 'Attendance Report' . ($userId ? ' (User ID: ' . $userId . ' not found)' : ' (No User ID provided)');
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn() => route('filament.client.pages.attendance', [
                    'tenant' => filament()->getTenant(),
                    'record' => request()->get('record')
                ]))
                ->openUrlInNewTab(false),
        ];
    }
}
