<?php

namespace App\Filament\Client\Resources\LeaveResource\Pages;

use App\Filament\Client\Resources\LeaveResource;
use App\Models\LeaveLog;
use App\Models\Shift;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateLeave extends CreateRecord
{
    protected static string $resource = LeaveResource::class;
    protected static bool $canCreateAnother = false;
    protected static ?string $title = 'Create Leave Request';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';
        $user = Auth::user();
        if (in_array($data['type'], ['half_day', 'short_leave'])) {
            $data['ending_date'] = $data['starting_date'];
        }
        if ($data['type'] === 'regular') {

            if ($user && $user->shift_name) {
                $shift = Shift::where('name', $user->shift_name)->where('team_id', Filament::getTenant()->id)->first();

                if ($shift) {
                    $data['starting_time'] = $shift->starting_time;
                    $data['ending_time'] = $shift->ending_time;
                }
            }
        }
        if ($data['type'] === 'half_day') {
            if ($user && $user->shift_name) {
                $shift = Shift::where('name', $user->shift_name)->where('team_id', Filament::getTenant()->id)->first();

                if ($shift) {
                    $data['starting_time'] = $shift->half_day_check_in;
                    $data['ending_time'] = $shift->half_day_check_out;
                }
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $currentRole = Auth::user()->roles->first();
        $roleId = $currentRole ? $currentRole->id : Auth::user()->id;
        LeaveLog::create([
            'leave_id' => $this->record->id,
            'role_id'  => $roleId,
            'level'    => 1,
            'status'   => 'pending',
            'remarks'  => '',
        ]);
    }
}
