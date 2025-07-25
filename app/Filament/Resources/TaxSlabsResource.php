<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxSlabsResource\Pages;
use App\Models\TaxSlabs;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Validation\Rule;

class TaxSlabsResource extends Resource
{
    protected static ?string $model = TaxSlabs::class;

    protected static ?string $navigationGroup = 'Payroll';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('country_id')
                                    ->relationship('country', 'name')
                                    ->label('Country')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')->label('Country Name')->required()->unique(table: 'countries', column: 'name'),
                                    ])
                                    ->preload()
                                    ->required(),

                                TextInput::make('salary_currency')
                                    ->label('Currency')
                                    ->hint('Three letter currency code (e.g. USD, EUR, etc.)')
                                    ->required(),

                                DatePicker::make('financial_year_start')
                                    ->label('Financial Year Start')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d F Y')
                                    ->rules([
                                        Rule::unique('tax_slabs', 'financial_year_start')->where(function ($query) use ($form) {
                                            return $query->where('country_id', $form->getLivewire()->data['country_id']);
                                        })->ignore($form->getRecord())
                                    ]),

                                DatePicker::make('financial_year_end')
                                    ->label('Financial Year End')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d F Y'),

                            ]),

                        TableRepeater::make('slabs_data')
                            ->label('Tax Brackets')
                            ->schema([
                                TextInput::make('min_annual_salary')
                                    ->label('Minimum Salary')
                                    ->key('min_annual_salary')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->columnSpan(1),
                                TextInput::make('max_annual_salary')
                                    ->label('Maximum Salary')
                                    ->key('max_annual_salary')
                                    ->numeric()
                                    ->minValue(0)
                                    ->columnSpan(1),
                                TextInput::make('tax_percentage')
                                    ->label('Tax Percentage (%)')
                                    ->key('tax_percentage')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%')
                                    ->nullable()
                                    ->columnSpan(1),
                                TextInput::make('additional_tax')
                                    ->label('Fixed Tax Amount')
                                    ->key('additional_tax')
                                    ->numeric()
                                    ->minValue(0)
                                    ->nullable()
                                    ->columnSpan(1),
                            ])
                            ->reorderable(false)
                            ->collapsible(false)
                            ->columns(3)
                            ->createItemButtonLabel('Add Tax Bracket')
                            ->defaultItems(1)
                    ])
                    ->columns(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Split::make([
                    TextColumn::make('country.name')->searchable(),
                    TextColumn::make('salary_currency'),
                    TextColumn::make('financial_year_start')
                        ->label('Financial Year')
                        ->formatStateUsing(function ($record) {
                            $start = \Carbon\Carbon::parse($record->financial_year_start)->format('j F Y');
                            $end = \Carbon\Carbon::parse($record->financial_year_end)->format('j F Y');
                            return "{$start} - {$end}";
                        }),
                ]),

                Panel::make([
                    Stack::make([
                        TextColumn::make('slabs_data')
                            ->label('Tax Brackets')
                            ->formatStateUsing(function ($state, $record) {
                                $slabs = $record->slabs_data;
                                $currency = $record->salary_currency;

                                if (is_string($slabs)) {
                                    $slabs = json_decode($slabs, true);
                                }

                                if (!is_array($slabs)) return '-';

                                return collect($slabs)
                                    ->map(function ($item) use ($currency) {
                                        $startValue = (float)($item['min_annual_salary'] ?? 0);
                                        $endValue = (float)($item['max_annual_salary'] ?? 0);

                                        $start = number_format($startValue);
                                        $end = number_format($endValue);
                                        $startMinusOne = number_format($startValue - 1);

                                        $rate = $item['tax_percentage'] ?? '0';
                                        $fixed = $item['additional_tax'] ?? '0';

                                        return $endValue > 0
                                            ? "<strong>{$start} - {$end} :</strong> {$currency} {$fixed} + {$rate}% of the amount exceeding {$currency} {$startMinusOne}"
                                            : "<strong>Above {$start} :</strong> {$currency} {$fixed} + {$rate}% of the amount exceeding {$currency} {$start}";
                                    })
                                    ->implode('<br>');
                            })
                            ->html()
                            ->wrap(),
                    ]),
                ])->collapsible(),


            ])
            ->defaultSort('country_id', 'asc')
            ->searchPlaceholder('Search Country')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaxSlabs::route('/'),
            'create' => Pages\CreateTaxSlabs::route('/create'),
            'edit' => Pages\EditTaxSlabs::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return "{$record->country->name}";
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['country.name'];
    }
}
