<?php

namespace App\Filament\Client\Resources\PayRunResource\Pages;

use App\Facades\Helper;
use App\Filament\Client\Resources\PayRunResource;
use App\Models\PayRun;
use App\Models\Payroll;
use App\Models\SalaryComponent;
use App\Services\PayrollCalculationService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;

class EditPayroll extends Page
{
    use InteractsWithFormActions;
    protected static string $resource = PayRunResource::class;
    protected static string $view = 'filament.client.resources.pay-run-resource.pages.edit-payroll';

    public PayRun $payRun;
    public ?Payroll $payroll = null;
    public ?array $data = [];

    public function mount($payrun, $record): void
    {
        $this->payroll = Payroll::findOrFail($record);
        $this->payRun = PayRun::findOrFail($payrun);
        $this->isFinalized = $this->payroll->status === 'finalized';
        $this->form->fill($this->getFormData());
    }

    protected function getFormData(): array
    {
        if (!$this->payroll) {
            return [];
        }

        $components = $this->fetchApplicableComponents($this->payroll);

        $formData = [
            'apply_increment' => $this->payroll->applied_increment_amount > 0,
            'increment_value' => $this->payroll->applied_increment_amount,
            'earnings' => $components['earnings'],
            'deductions' => $components['deductions'],
            'ad_hoc_earnings' => $this->payroll->earnings_data['ad_hoc_earnings'] ?? [],
            'ad_hoc_deductions' => $this->payroll->deductions_data['ad_hoc_deductions'] ?? [],
            'overtime_earning_amount' => $this->payroll->attendance_data['overtime_earning_amount'] ?? 0,
            'late_deduction_amount' => $this->payroll->attendance_data['late_minutes_deduction_amount'] ?? 0,
            'absent_deduction_amount' => $this->payroll->attendance_data['absent_deduction_amount'] ?? 0,
            'deduct_late_penalties' => $this->payroll->deduct_late_penalties ?? true,
            'deduct_absent_penalties' => $this->payroll->deduct_absent_penalties ?? true,
            'apply_overtime_earnings' => $this->payroll->apply_overtime_earnings ?? true,
            'fund_data' => $this->payroll->fund_data,
        ];

        // Logic to set fund toggle states
        $adHocEarnings = $formData['ad_hoc_earnings'];
        $adHocEarningIds = collect($adHocEarnings)->pluck('id');
        $employeeFunds = Helper::getEmployeeFund($this->payroll->user);

        foreach ($employeeFunds as $fund) {
            $toggleName = 'fund_toggle_' . $fund->id;
            $fundEarningId = 'adhoc_earning_fund_id' . $fund->id;
            $formData[$toggleName] = $adHocEarningIds->contains($fundEarningId);
        }

        return $formData;
    }

    protected function fetchApplicableComponents(Payroll $payroll): array
    {
        if ($payroll->status === 'finalized') {
            return [
                'earnings' => $payroll->earnings_data['custom_earnings_applied'] ?? [],
                'deductions' => $payroll->deductions_data['custom_deductions_applied'] ?? [],
            ];
        }

        // Draft payroll: calculate from active components
        $employee = $payroll->user;
        if (!$employee) {
            return ['earnings' => [], 'deductions' => []];
        }

        $previouslyAppliedOneTimeDeductionIds = \App\Models\Payroll::where('user_id', $employee->id)
            ->where('id', '!=', $payroll->id) // Exclude the payroll being edited
            ->get()
            ->flatMap(fn($p) => $p->applied_one_time_deductions ?? [])
            ->filter()
            ->unique()
            ->toArray();

        $components = Filament::getTenant()
            ->salaryComponents()
            ->where('is_active', true)
            ->get();

        $earnings = [];
        $deductions = [];

        $savedEarnings = collect($payroll->earnings_data['custom_earnings_applied'] ?? [])->keyBy('id');
        $savedDeductions = collect($payroll->deductions_data['custom_deductions_applied'] ?? [])->keyBy('id');

        foreach ($components as $component) {
            $isEarning = $component->component_type === 'earning';

            if (!$isEarning && $component->is_one_time_deduction) {
                if (in_array($component->id, $previouslyAppliedOneTimeDeductionIds)) {
                    continue;
                }
            }

            $savedComponent = $isEarning
                ? $savedEarnings->get($component->id)
                : $savedDeductions->get($component->id);

            $amount = isset($savedComponent['amount_input'])
                ? (float)$savedComponent['amount_input']
                : (float)$component->amount;

            $data = [
                'id' => $component->id,
                'title' => $component->title,
                'value_type' => $component->value_type,
                'amount' => $amount,
            ];

            if ($isEarning) {
                $earnings[] = $data;
            } else {
                $deductions[] = $data;
            }
        }

        $customEarnings = $payroll->earnings_data['ad_hoc_earnings'] ?? [];
        $customDeductions = $payroll->deductions_data['ad_hoc_deductions'] ?? [];

        return [
            'earnings' => $earnings,
            'deductions' => $deductions,
            'custom_earnings' => $customEarnings,
            'custom_deductions' => $customDeductions,
        ];
    }

    public function form(Form $form): Form
    {
        $adminId = $this->payRun->team_id;
        $payrollService = app(PayrollCalculationService::class);
        $currency = $payrollService->getCurrencySymbolForAdmin($adminId);

        return $form
            ->schema([
                Section::make('')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Placeholder::make('base_salary')
                            ->label('Base Salary')
                            ->content(fn() => $currency . ' ' . number_format($this->payroll?->base_salary ?? 0)),

                        // -- Increment Logic --
                        Grid::make(3)->schema([
                            Forms\Components\Toggle::make('apply_increment')
                                ->label('Apply Increment')
                                ->offIcon('heroicon-s-x-mark')
                                ->onIcon('heroicon-s-check')
                                ->disabled(fn() => isset($this->payroll) && $this->payroll->applied_increment_amount > 0)
                                ->reactive(),

                            Forms\Components\Select::make('increment_type')
                                ->label('Type')
                                ->options([
                                    'number' => 'Fixed Amount',
                                    'percentage' => 'Percentage of Base Salary',
                                ])
                                ->visible(fn($get) => $get('apply_increment') && !(
                                    isset($this->payroll) && $this->payroll->applied_increment_amount > 0
                                ))
                                ->required(fn($get) => $get('apply_increment'))
                                ->reactive(),

                            Forms\Components\TextInput::make('increment_value')
                                ->label('Amount')
                                ->numeric()
                                ->minValue(0)
                                ->prefix(function ($get) use ($currency) {
                                    $isIncrementApplied = isset($this->payroll) && $this->payroll->applied_increment_amount > 0;
                                    $type = $get('increment_type');

                                    return $isIncrementApplied ? $currency . ' ' : (
                                        $type === 'number' ? $currency . ' ' : ($type === 'percentage' ? '% ' : null)
                                    );
                                })
                                ->visible(fn($get) => $get('apply_increment'))
                                ->disabled(fn() => isset($this->payroll) && $this->payroll->applied_increment_amount > 0)
                                ->reactive(),
                        ])->columnSpanFull(),
                        Forms\Components\Fieldset::make('Fund Earnings')
                            ->schema(
                                collect(Helper::getEmployeeFund($this->payroll->user))->map(function ($fund) use ($currency) {
                                    return Forms\Components\Toggle::make('fund_toggle_' . $fund->id)
                                        ->label($fund->name)
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set, callable $get) use ($fund) {
                                            $earnings = $get('ad_hoc_earnings') ?? [];
                                            $existing = collect($earnings)->firstWhere('id', 'adhoc_earning_fund_id' . $fund->id);
                                            if ($state && !$existing) {
                                                $earnings[] = [
                                                    'id' => 'adhoc_earning_fund_id' . $fund->id,
                                                    'title' => $fund->name,
                                                    'value_type' => 'number',
                                                    'amount_input' => Helper::getEmployeeDeductedFund($this->payroll->user, $fund),
                                                    'tax_status' => 'taxable',
                                                ];
                                                $set('ad_hoc_earnings', $earnings);
                                            }

                                            if (!$state && $existing) {
                                                $earnings = collect($earnings)
                                                    ->reject(fn($item) => $item['id'] === 'adhoc_earning_fund_id' . $fund->id)
                                                    ->values()
                                                    ->toArray();
                                                $set('ad_hoc_earnings', $earnings);
                                            }
                                        });
                                })->toArray()
                            )
                            ->columns(3)
                            ->visible(fn() => $this->checkFunds()),

                        Repeater::make('earnings')
                            ->label('Earnings')
                            ->columnSpanFull()
                            ->schema([
                                Forms\Components\Hidden::make('id'),
                                Forms\Components\TextInput::make('title')->disabled(),
                                Forms\Components\TextInput::make('amount')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->prefix(fn(callable $get) => $get('value_type') === 'percentage' ? '% ' : $currency . ' ')
                                    ->reactive(),
                            ])->columns(2)->addable(false)->deletable(false)->reorderable(false),

                        Forms\Components\Repeater::make('ad_hoc_earnings')
                            ->label('')
                            ->columnSpanFull()
                            ->schema([
                                Forms\Components\Hidden::make('id')->default(uniqid('adhoc_earning_custom_id')),
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->reactive()
                                    ->readOnly(fn(callable $get) => str($get('id'))->startsWith('adhoc_earning_fund_id')),


                                Forms\Components\Select::make('value_type')
                                    ->label('Type')
                                    ->options([
                                        'number' => 'Fixed Amount',
                                        'percentage' => 'Percentage',
                                    ])
                                    ->default('number')
                                    ->required()
                                    ->reactive()
                                    ->disabled(fn(callable $get) => str($get('id'))->startsWith('adhoc_earning_fund_id')),

                                Forms\Components\TextInput::make('amount_input')
                                    ->label('Amount')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->reactive()
                                    ->readOnly(fn(callable $get) => str($get('id'))->startsWith('adhoc_earning_fund_id')),

                                Forms\Components\Select::make('tax_status')
                                    ->label('Tax Status')
                                    ->options([
                                        'taxable' => 'Taxable',
                                        'non-taxable' => 'Non-Taxable',
                                    ])
                                    ->required()
                                    ->default('taxable')
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->reorderable(false)
                            ->addActionLabel('Add One-Time Earning'),

                        // -- Deductions --


                        Repeater::make('deductions')
                            ->label('Deductions')
                            ->columnSpanFull()
                            ->schema([
                                Forms\Components\Hidden::make('id')->reactive(),
                                Forms\Components\TextInput::make('title')->disabled(),
                                Forms\Components\TextInput::make('amount')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->prefix(fn(callable $get) => $get('value_type') === 'percentage' ? '% ' : $currency . ' ')
                                    ->reactive(),
                            ])->columns(2)->addable(false)->deletable(false)->reorderable(false),

                        Repeater::make('ad_hoc_deductions')
                            ->label('')
                            ->columnSpanFull()
                            ->schema([
                                Forms\Components\TextInput::make('title')->required(),
                                Forms\Components\Select::make('value_type')
                                    ->label('Type')
                                    ->options([
                                        'number' => 'Fixed Amount',
                                        'percentage' => 'Percentage',
                                    ])
                                    ->default('number')
                                    ->required()
                                    ->reactive(),

                                Forms\Components\TextInput::make('amount_input')
                                    ->label('Amount')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->prefix(function (callable $get) use ($currency) {
                                        return $get('value_type') === 'percentage' ? '%' : $currency;
                                    })
                                    ->reactive(),
                            ])->columns(3)->reorderable(false)->addActionLabel('Add One-Time Deduction'),

                        // -- Attendance Adjustments --
                        Forms\Components\Fieldset::make('Attendance Adjustments')
                            ->columnSpanFull()
                            ->schema([
                                Forms\Components\TextInput::make('overtime_earning_amount')->label('Overtime Earning')->numeric()->disabled(),
                                Forms\Components\Toggle::make('apply_overtime_earnings')->label('Apply'),

                                Forms\Components\TextInput::make('late_deduction_amount')->label('Late Deduction')->numeric()->disabled(),
                                Forms\Components\Toggle::make('deduct_late_penalties')->label('Apply'),

                                Forms\Components\TextInput::make('absent_deduction_amount')->label('Absent Deduction')->numeric()->disabled(),
                                Forms\Components\Toggle::make('deduct_absent_penalties')->label('Apply'),
                            ])->columns(2),
                        // -- Attendance Adjustments --
                        Forms\Components\Fieldset::make('Fund')
                            ->columnSpanFull()
                            ->schema([
                                Repeater::make('fund_data')
                                    ->label('')
                                    ->columnSpanFull()
                                    ->schema([
                                        Forms\Components\TextInput::make('amount_input')
                                            ->label(fn(callable $get) => $get('title') ?: 'Amount')
                                            ->numeric()
                                            ->minValue(0)
                                            ->required()
                                            ->reactive()
                                            ->disabled(),
                                    ])->columns(2)->addable(false)->deletable(false)->reorderable(false),
                            ])->columns(2)->visible(fn() => $this->checkFunds()),

                    ]),
            ])
            ->statePath('data');
    }

    protected function checkFunds(): bool
    {
        return $this->payroll->user->funds()
            ->wherePivot('team_id', Filament::getTenant()->id)
            ->exists();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->url(fn() => $this->getResource()::getUrl())
                ->icon('heroicon-o-arrow-left')
                ->color('gray'),
        ];
    }
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (!$this->payroll) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('No payroll record found.')
                ->send();
            return;
        }

        $predefinedEarningsInput = collect($data['earnings'] ?? [])
            ->map(function ($item) {
                $component = SalaryComponent::find($item['id']);
                if ($component) {
                    return [
                        'id' => $component->id,
                        'name' => $component->name,
                        'title' => $component->title,
                        'type' => $component->component_type,
                        'amount_input' => (float)$item['amount'],
                    ];
                }
            })->toArray();
        $predefinedDeductionsInput = collect($data['deductions'] ?? [])
            ->map(function ($item) {
                $component = SalaryComponent::find($item['id']);
                if ($component) {
                    return [
                        'id' => $component->id,
                        'name' => $component->name,
                        'title' => $component->title,
                        'type' => $component->component_type,
                        'amount_input' => (float)$item['amount'],
                    ];
                }
            })->toArray();

        $adHocEarningsInput = $data['ad_hoc_earnings'] ?? [];
        $adHocDeductionsInput = $data['ad_hoc_deductions'] ?? [];

        $payrollCalculationService = app(PayrollCalculationService::class);
        $updatedPayrollData = $payrollCalculationService->recalculateEmployeePayrollData(
            $this->payroll,
            $this->payroll->base_salary,
            $predefinedEarningsInput,
            $predefinedDeductionsInput,
            $adHocEarningsInput,
            $adHocDeductionsInput,
            $data['apply_increment'] ?? false,
            $data['increment_type'] ?? 'number',
            (float)($data['increment_value'] ?? 0),
            $data['deduct_late_penalties'] ?? true,
            $data['deduct_absent_penalties'] ?? true,
            $data['apply_overtime_earnings'] ?? true
        );

        $this->payroll->update($updatedPayrollData);

        Notification::make()
            ->success()
            ->title('Payroll Updated Successfully')
            ->send();

        $this->redirect(static::getResource()::getUrl('view', ['record' => $this->payRun]));
    }

    public function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label('Cancel')
            ->url(static::getResource()::getUrl('view', ['record' => $this->payRun]))
            ->color('gray');
    }

    public function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label('Save Changes')
            ->action('save')
            ->color('primary');
    }

    public function getTitle(): string
    {
        return ($this->payroll?->user?->name ?? 'Unknown Employee');
    }
}
