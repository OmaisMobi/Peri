<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\DeviceResource\Pages;
use App\Models\Device;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Guava\FilamentKnowledgeBase\Contracts\HasKnowledgeBase;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;

class DeviceResource extends Resource implements HasKnowledgeBase
{
    protected static ?string $model = Device::class;

    protected static ?string $navigationLabel = 'Device Configuration';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 5;

    protected static ?string $tenantOwnershipRelationshipName = 'team';
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()
            ->count();
    }
    public static function form(Form $form): Form
    {
        return $form->schema(
            [
                Card::make()->schema(
                    [
                        TextInput::make('device_name')
                            ->label('Device Name')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),

                        TextInput::make('device_ip_address')
                            ->label('Device IP Address')
                            ->ip()
                            ->nullable(),

                        TextInput::make('device_external_port')
                            ->label('External Port')
                            ->numeric()
                            ->nullable()
                    ]
                ),
            ]
        );
    }

    public static function getDocumentation(): array
    {
        return [
            KnowledgeBase::model()::find('devices.introduction'),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('device_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('device_ip_address')->label('IP Address')->searchable(),
                Tables\Columns\TextColumn::make('device_external_port')->label('External Port')->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->can('device.view') || Auth::user()->can('device.manage'));
    }

    public static function canCreate(): bool
    {
        return Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->can('device.manage'));
    }

    public static function canEdit($record): bool
    {
        return Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->can('device.manage'));
    }

    public static function canDelete($record): bool
    {
        return Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->can('device.manage'));
    }
}
