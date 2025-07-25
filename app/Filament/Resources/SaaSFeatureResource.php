<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaaSFeatureResource\Pages;
use App\Filament\Resources\SaaSFeatureResource\RelationManagers;
use App\Models\SaasFeature;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SaaSFeatureResource extends Resource
{
    protected static ?string $model = SaasFeature::class;

    public static ?string $label = 'Features';

    protected static ?string $navigationGroup = 'Website Pages';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->required(),
                TextInput::make('description')->required(),
                TextInput::make('icon')->label('Bootstrap Icon'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Title'),
                Tables\Columns\TextColumn::make('description')->label('Description'),
                Tables\Columns\TextColumn::make('icon')->label('Icon'),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListSaaSFeatures::route('/'),
            'create' => Pages\CreateSaaSFeature::route('/create'),
            'edit' => Pages\EditSaaSFeature::route('/{record}/edit'),
        ];
    }
}
