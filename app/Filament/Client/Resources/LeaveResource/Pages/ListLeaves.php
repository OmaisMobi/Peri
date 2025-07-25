<?php

namespace App\Filament\Client\Resources\LeaveResource\Pages;

use App\Facades\Helper;
use App\Filament\Client\Resources\LeaveResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Components\Tab;

class ListLeaves extends ListRecords
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];

        $user = Auth::user();


        if (Helper::isAssignUsers()) {
            $tabs[] = Tab::make('My Leaves')
                ->icon('heroicon-m-user')
                ->extraAttributes(['data-cy' => 'my-leaves-tab'])
                ->modifyQueryUsing(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                });

            $tabs[] = Tab::make('Team Leaves')
                ->icon('heroicon-m-user-group')
                ->extraAttributes(['data-cy' => 'assigned-tab'])
                ->modifyQueryUsing(function ($query) use ($user) {
                    return $query->whereIn('user_id', Helper::getAssignUsersIds())->where('user_id', '!=', $user->id);
                });
        }
        return $tabs;
    }
}
