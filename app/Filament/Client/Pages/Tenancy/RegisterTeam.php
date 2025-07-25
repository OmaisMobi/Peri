<?php

namespace App\Filament\Client\Pages\Tenancy;

use App\Http\Middleware\VerifyBillableIsSubscribed;
use App\Models\Team;
use App\Models\Country;
use App\Models\Permission;
use App\Models\Role;
use DateTimeZone;
use DiogoGPinto\AuthUIEnhancer\Pages\Auth\Concerns\HasCustomLayout;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;

class RegisterTeam extends RegisterTenant
{
    use HasCustomLayout;
    protected static string | array $withoutRouteMiddleware = VerifyBillableIsSubscribed::class;

    public static function getLabel(): string
    {
        return 'Register New Company';
    }

    public function getRegisterFormAction(): Action
    {
        return Action::make('register')
            ->submit('register');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('logo')
                    ->label('Company Logo')
                    ->image()
                    ->disk('public')
                    ->directory('uploads/companies/temp')
                    ->visibility('public')
                    ->maxSize(2048) // 2MB
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->deletable()
                    ->downloadable()
                    ->previewable()
                    ->helperText('Accepted Formats: JPEG, PNG, or WebP, max 2MB'),

                TextInput::make('name')
                    ->label('Company Name')
                    ->required(),

                Select::make('country_id')
                    ->label('Country')
                    ->options(fn() => Country::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->live()
                    ->required(),

                Select::make('timezone')
                    ->label('Timezone')
                    ->options(
                        collect(DateTimeZone::listIdentifiers())
                            ->mapWithKeys(fn($tz) => [$tz => $tz])
                            ->toArray()
                    )
                    ->searchable()
                    ->required()
            ]);
    }

    protected function handleRegistration(array $data): Team
    {
        $team = Team::create($data);
        $adminRole = Role::create([
            'name' => 'Admin',
            'guard_name' => 'web',
            'team_id' => $team->id,
            'is_default' => true,
        ]);
        setPermissionsTeamId($team->id);
        $team->members()->attach(Auth::id());
        Auth::user()->assignRole($adminRole);
        $permissions = Permission::all();
        $adminRole->syncPermissions($permissions);
        $this->teamInitailize($team);
        return $team;
    }
    
    protected function teamInitailize(Team $team)
    {
        $amsRole = Role::create([
            'name' => 'AMS Manager',
            'guard_name' => 'web',
            'team_id' => $team->id,
            'is_default' => true,
        ]);

        $payrollRole = Role::create([
            'name' => 'Payroll Manager',
            'guard_name' => 'web',
            'team_id' => $team->id,
            'is_default' => true,
        ]);

        $amsRole->givePermissionTo([
            'employees.manage',
            'departments.manage',
            'device.manage',
            'leaveType.manage',
            'attendancePolicies.manage',
            'shifts.manage',
            'biometric.approve',
            'holiday.manage',
        ]);

        $payrollRole->givePermissionTo([
            'employees.manage',
            'departments.manage',
            'payroll.create',
            'payroll.approve',
            'payroll.manageRecords'
        ]);
    }
}
