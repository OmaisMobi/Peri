<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Contact;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;

class OfflineRequests extends Page implements Tables\Contracts\HasTable
{
    use InteractsWithTable;

    protected static ?string $title = 'Messages';
    protected static ?string $navigationLabel = 'Messages';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    protected static ?string $navigationBadgeTooltip = 'Contact Messages';
    protected static string $view = 'filament.pages.offline-requests';

    public static function getNavigationBadge(): ?string
    {
        return Contact::count();
    }

    // Query for fetching data
    protected function getTableQuery(): Builder
    {
        return Contact::query();
    }

    // Define the table columns
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Name')
                ->searchable(),

            Tables\Columns\TextColumn::make('email')
                ->label('Email')
                ->searchable(),

            Tables\Columns\TextColumn::make('message')
                ->label('Message')
                ->limit(50)
                ->tooltip(fn($record) => $record->message),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Submitted At')
                ->dateTime('M d, Y H:i'),
        ];
    }

    // Add a delete action to the table
    protected function getTableActions(): array
    {
        return [
            DeleteAction::make()
                ->modalHeading('Delete Contact Message')
                ->modalDescription('Are you sure you want to delete this message? This action cannot be undone.')
                ->button('Delete'),
        ];
    }
}
