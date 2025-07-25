<?php

namespace App\Filament\Client\Pages\Tenancy;

use App\Http\Middleware\VerifyBillableIsSubscribed;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use DateTimeZone;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EditTeamProfile extends EditTenantProfile
{
    protected static string | array $withoutRouteMiddleware = VerifyBillableIsSubscribed::class;

    public static function getLabel(): string
    {
        return 'Company profile';
    }
    public static function canView(Model $tenant): bool
    {
        try {
            return Auth::user()->hasRole('Admin');
        } catch (AuthorizationException $exception) {
            return $exception->toResponse()->allowed();
        }
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        Group::make()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                FileUpload::make('logo')
                                                    ->label('Company Logo')
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory(fn() => 'uploads/' . Filament::getTenant()->slug . '/team')
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
                                            ])->columnSpan(1),

                                    ])->columnSpanFull(),

                            ])
                            ->columnSpan(2),

                    ]),

            ]);
    }
}
