<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Subscriptions';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('trx')
                    ->label('Transaction ID')
                    ->searchable(),
                TextColumn::make('method_name')
                    ->label('Method Name')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(function (Payment $record) {
                        return Number::currency($record->amount, in: $record->method_currency) . " + " . Number::currency($record->charge, in: $record->method_currency) . '<br>' . Number::currency(($record->amount + $record->charge), in: $record->method_currency);
                    })->html(),

                TextColumn::make('rate')
                    ->label('Conversion')
                    ->formatStateUsing(function (Payment $record) {
                        return Number::currency(1, in: 'USD') . " = " . Number::currency($record->rate, in: $record->method_currency) . '<br>' . Number::currency($record->final_amount, in: 'USD');
                    })->html(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn($record) => match ($record->status) {
                        0 => 'Processing',
                        1 => 'Completed',
                        2 => 'Cancelled',
                        default => 'Initiated',
                    })
                    ->icon(fn($record) => match ($record->status) {
                        0 => 'heroicon-o-clock',
                        1 => 'heroicon-s-check-circle',
                        2 => 'heroicon-s-x-circle',
                        default => 'heroicon-s-x-circle',
                    })
                    ->color(fn($record) => match ($record->status) {
                        0 => 'info',
                        1 => 'success',
                        2 => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y h:iA')
                    ->description(fn($record): string => Carbon::parse($record->created_at)->diffForHumans()),
            ])
            ->searchPlaceholder('Search Transaction ID')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        0 => 'Processing',
                        1 => 'Completed',
                        2 => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn($record) => match ($record->status) {
                        0 => 'Processing',
                        1 => 'Completed',
                        2 => 'Cancelled',
                        default => 'Initiated',
                    })
                    ->icon(fn($record) => match ($record->status) {
                        0 => 'heroicon-o-clock',
                        1 => 'heroicon-s-check-circle',
                        2 => 'heroicon-s-x-circle',
                        default => 'heroicon-s-x-circle',
                    })
                    ->color(fn($record) => match ($record->status) {
                        0 => 'info',
                        1 => 'success',
                        2 => 'danger',
                        default => 'secondary',
                    }),
                TextEntry::make('created_at')
                    ->label('Date')
                    ->dateTime(),
                TextEntry::make('trx')
                    ->label('Transaction Number'),
                TextEntry::make('customer.name')
                    ->label('Name')
                    ->formatStateUsing(fn(Payment $record) => $record->customer['name'] ?? 'N/A'),
                TextEntry::make('customer.email')
                    ->label('Email')
                    ->formatStateUsing(fn(Payment $record) => $record->customer['email'] ?? 'N/A'),
                TextEntry::make('customer.mobile')
                    ->label('Mobile')
                    ->formatStateUsing(fn(Payment $record) => $record->customer['mobile'] ?? 'N/A'),
                TextEntry::make('method_name')
                    ->label('Payment Method'),
                TextEntry::make('method_code')
                    ->label('Method Code')
                    ->formatStateUsing(fn($state) => \Illuminate\Support\Str::limit($state, 50)),
                TextEntry::make('amount')
                    ->label('Amount')
                    ->money(fn($record) => $record->method_currency ?? 'USD', locale: 'en'),
                TextEntry::make('charge')
                    ->label('Charge')
                    ->money(fn($record) => $record->method_currency ?? 'USD', locale: 'en'),
                TextEntry::make('rate')
                    ->label('Rate')
                    ->formatStateUsing(fn(Payment $record) => Number::currency(1, in: 'USD') . " = " . Number::currency($record->rate, in: $record->method_currency))
                    ->html(),
                TextEntry::make('final_amount')
                    ->label('After Rate Conversion')
                    ->money(fn($record) => $record->method_currency ?? 'USD', locale: 'en'),
            ])
            ->columns(2);
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
            'index' => Pages\ListPayments::route('/'),
        ];
    }
}
