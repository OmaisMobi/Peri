<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplatesResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\HtmlString;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class EmailTemplatesResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;
    protected static ?string $navigationGroup = 'Email Templates';
    protected static ?string $modelLabel = 'Templates';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')->required()->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('subject')->required(),
                        TinyEditor::make('body')
                            ->label('Content'),
                        Forms\Components\TagsInput::make('variables')
                            ->label('Template Variables')
                            ->placeholder('Add variable names')
                            ->separator(',')
                            ->suggestions([
                                'email_title',
                                'company_name',
                                'main_title',
                                'user_name',
                                'cta_url',
                                'cta_text',
                                'closing_message'
                            ])
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('subject'),
                Tables\Columns\TextColumn::make('created_at')->since(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => route('template.preview', [
                        'id' => $record->id
                    ]))
                    ->openUrlInNewTab()
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
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplates::route('/create'),
            'edit' => Pages\EditEmailTemplates::route('/{record}/edit'),
        ];
    }
}
