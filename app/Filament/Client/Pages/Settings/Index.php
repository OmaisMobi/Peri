<?php

namespace App\Filament\Client\Pages\Settings;

use App\Filament\Client\Pages\Billing;
use App\Filament\Client\Pages\Policy;
use App\Filament\Client\Pages\Tenancy\EditTeamProfile;
use App\Filament\Client\Resources\EmployeeResource;
use App\Filament\Client\Resources\EmployeeResource\Pages\ListEmployees;
use App\Filament\Client\Resources\RoleResource\Pages\ListRoles;
use Kanuni\FilamentCards\Filament\Pages\CardsPage;
use Kanuni\FilamentCards\CardItem;

class Index extends CardsPage
{
    protected static ?string $slug = 'settings';
    protected static ?string $title = 'Settings';
    protected static bool $shouldRegisterNavigation = false;
    protected static function getCards(): array
    {
        return [
            CardItem::make(EditTeamProfile::class)
                ->title('Company')
                ->description('Manage Company Profile')
                ->icon('heroicon-o-building-office-2')
                ->group('General'),

            CardItem::make(Billing::class)
                ->title('Subscription')
                ->description('Manage Subscribed Plan')
                ->icon('heroicon-o-credit-card')
                ->group('General'),

            CardItem::make(Admins::class)
                ->title('Admins')
                ->description('System administrators')
                ->icon('heroicon-o-users')
                ->group('Users'),

            CardItem::make(AttendanceManagers::class)
                ->title('AMS Managers')
                ->description('Attendance Managers')
                ->icon('heroicon-o-finger-print')
                ->group('Users'),

            CardItem::make(PayrollManagers::class)
                ->title('Payroll Managers')
                ->description('Salary Managers')
                ->icon('heroicon-o-document-currency-dollar')
                ->group('Users'),

            CardItem::make(CEO::class)
                ->title('CEO')
                ->description('Chief Executive Officer')
                ->icon('heroicon-o-academic-cap')
                ->group('Users'),

            CardItem::make(ListEmployees::class)
                ->title('Employees')
                ->description('Employees')
                ->icon('heroicon-o-user')
                ->group('Users'),

            CardItem::make(ListRoles::class)
                ->title('Roles And Permissions')
                ->description('Manage Roles And Permissions')
                ->icon('heroicon-o-shield-check')
                ->group('Organization'),

            CardItem::make(Policy::class)
                ->title('Attendance Policy')
                ->description('Manage Attendance Policies')
                ->icon('heroicon-o-calendar-days')
                ->group('Organization'),
        ];
    }
}
