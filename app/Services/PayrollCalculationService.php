<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payroll;
use App\Models\TaxSlabs;
use App\Models\ShiftLog;
use App\Models\Leave;
use App\Models\SalaryComponent;
use App\Models\Holiday;
use App\Models\Team;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PayrollCalculationService
{
    protected HelperService $HelperService;
    protected LeaveService $leaveService;
    protected ?string $currencySymbol = null;

    public function __construct(HelperService $HelperService, LeaveService $leaveService)
    {
        $this->HelperService = $HelperService;
        $this->leaveService = $leaveService;
    }

    public function calculateEmployeePayrollData(
        int $userId,
        Carbon $periodStartDate,
        Carbon $periodEndDate,
        int $effectiveMonthIndicator,
        ?float $manualBaseSalaryOverride = null,
        array $customEarningsInput = [],
        array $customDeductionsInput = [],
        bool $applyIncrementForThisRun = false,
        string $incrementType = 'number',
        ?float $incrementValue = null,
        bool $configDeductLatePenalties = true,
        bool $configDeductAbsentPenalties = true,
        bool $configApplyOvertimeEarnings = true,
        string $configPenaltyTaxStatus = 'non-taxable'
    ): array {
        $user = Filament::getTenant()->users()->with(['assignedDepartment.department', 'assignedShift.shift'])->where('id', $userId)->first();
        $teamId = Filament::getTenant()->id;

        if ($teamId === null || !is_int($teamId)) {
            Log::error("PayrollCalculationService: Invalid admin_id for User ID {$userId}. admin_id value: " . print_r($teamId, true));
            throw new \InvalidArgumentException("User with ID {$userId} has an invalid or missing admin_id. Payroll calculation cannot proceed.");
        }

        $this->fetchCompanyCurrencySymbol($teamId);

        $salaryDetails = $this->fetchBaseSalaryDetails($user, $manualBaseSalaryOverride);
        $currentPeriodBaseSalary = $salaryDetails['base_salary_for_period'];
        $originalBaseSalaryForCalc = $salaryDetails['original_base_salary'];

        $incrementAmountAppliedThisRun = 0;
        if ($applyIncrementForThisRun && $incrementValue !== null && $incrementValue > 0) { // Added null check for $incrementValue
            $actual_increment = ($incrementType === 'percentage') ? ($originalBaseSalaryForCalc * ($incrementValue / 100)) : $incrementValue;
            $currentPeriodBaseSalary += $actual_increment;
            $incrementAmountAppliedThisRun = $actual_increment;
        }

        $workingData = $this->calculateWorkingDays($periodStartDate, $periodEndDate, $user);
        $totalDaysInPeriod = $periodStartDate->diffInDays($periodEndDate) + 1;

        $attendanceSummary = $this->calculateAttendanceSummary(
            $user,
            $periodStartDate,
            $periodEndDate,
            $workingData['workingDays']
        );

        $financialRates = $this->calculateFinancialRates(
            $user,
            $currentPeriodBaseSalary,
            $workingData['workingDays']
        );
        $perDayRate = $financialRates['per_day_rate'];
        $perMinuteRate = $financialRates['per_minute_rate'];

        $attendanceFinancialImpacts = $this->calculateAttendanceFinancialImpacts(
            $attendanceSummary['total_late_minutes'],
            $attendanceSummary['absent_days_count'] + $attendanceSummary['unpaid_leave_days_count'],
            $attendanceSummary['total_overtime_minutes'],
            $perMinuteRate,
            $perDayRate
        );

        $processedComponents = $this->processSalaryComponents(
            $user,
            $currentPeriodBaseSalary,
            $customEarningsInput,
            $customDeductionsInput,
            $periodStartDate
        );
        $totalCustomTaxableEarnings = $processedComponents['total_taxable_earnings'];
        $totalCustomNonTaxableEarnings = $processedComponents['total_non_taxable_earnings'];
        $totalCustomTaxableDeductions = $processedComponents['total_taxable_deductions'];
        $totalCustomNonTaxableDeductions = 0.0;

        $attendancePenaltiesValue = 0.0;
        if ($configDeductLatePenalties) $attendancePenaltiesValue += $attendanceFinancialImpacts['late_deduction_amount'];
        if ($configDeductAbsentPenalties) $attendancePenaltiesValue += $attendanceFinancialImpacts['absent_deduction_amount'];

        $attendanceEarningsValue = 0.0;
        if ($configApplyOvertimeEarnings) $attendanceEarningsValue += $attendanceFinancialImpacts['overtime_earning_amount'];

        $totalTaxableEarnings = $totalCustomTaxableEarnings + ($configPenaltyTaxStatus === 'taxable' ? $attendanceEarningsValue : 0.0);
        $totalTaxableDeductions = $totalCustomTaxableDeductions + ($configPenaltyTaxStatus === 'taxable' ? $attendancePenaltiesValue : 0.0);
        $totalNonTaxableEarnings = $totalCustomNonTaxableEarnings + ($configPenaltyTaxStatus === 'non-taxable' ? $attendanceEarningsValue : 0.0);
        $totalNonTaxableDeductions = $totalCustomNonTaxableDeductions + ($configPenaltyTaxStatus === 'non-taxable' ? $attendancePenaltiesValue : 0.0);

        $taxData = $this->calculateTax(
            $user,
            $teamId,
            $currentPeriodBaseSalary,
            $totalTaxableEarnings,
            $totalTaxableDeductions,
            $effectiveMonthIndicator,
            $periodStartDate
        );
        foreach ($processedComponents['final_fund_deductions_for_db'] as $fund) {
            $total_fund_deduct = $fund["calculated_amount"];
            $totalNonTaxableDeductions += $total_fund_deduct;
        }
        $netMonthlyPayableSalary = $taxData['monthly_taxable_base']
            + $totalNonTaxableEarnings
            - $totalNonTaxableDeductions
            - $taxData['monthly_tax_calculated'];
        return [
            'user_id' => $user->id,
            'team_id' => Filament::getTenant()->id,
            'date_range_start' => $periodStartDate->toDateString(),
            'date_range_end' => $periodEndDate->toDateString(),
            'month_indicator' => $taxData['months_remaining_in_fy'],
            'base_salary' => round($currentPeriodBaseSalary),
            'applied_increment_amount' => round($incrementAmountAppliedThisRun),
            'earnings_data' => $processedComponents['final_earnings_for_db'],
            'deductions_data' => $processedComponents['final_deductions_for_db'],
            'fund_data' => ($processedComponents['final_fund_deductions_for_db']) ?? null,
            'applied_one_time_deductions' => $processedComponents['applied_one_time_deduction_ids'],
            'attendance_data' => [
                'total_days' => $totalDaysInPeriod,
                'working_days' => $workingData['workingDays'],
                'actual_working_days' => $attendanceSummary['actual_working_days'],
                'present_days_count' => $attendanceSummary['present_days_count'],
                'absent_days_count' => $attendanceSummary['absent_days_count'],
                'paid_leave_days_count' => $attendanceSummary['paid_leave_days_count'],
                'unpaid_leave_days_count' => $attendanceSummary['unpaid_leave_days_count'],
                'total_late_minutes' => $attendanceSummary['total_late_minutes'],
                'total_overtime_minutes' => $attendanceSummary['total_overtime_minutes'],
                'late_minutes_deduction_amount' => $configDeductLatePenalties ? $attendanceFinancialImpacts['late_deduction_amount'] : 0.0,
                'absent_deduction_amount' => $configDeductAbsentPenalties ? $attendanceFinancialImpacts['absent_deduction_amount'] : 0.0,
                'overtime_earning_amount' => $configApplyOvertimeEarnings ? $attendanceFinancialImpacts['overtime_earning_amount'] : 0.0,
                'per_day_rate_used' => $perDayRate,
                'per_minute_rate_used' => $perMinuteRate, // Often needs more precision
            ],
            'tax_data' => $taxData, // taxData already has rounded values from calculateTax
            'net_payable_salary' => $netMonthlyPayableSalary,
            'currency_symbol' => $this->currencySymbol,
            'original_base_salary_before_run_increment' => round($originalBaseSalaryForCalc),
        ];
    }

    private function fetchCompanyCurrencySymbol(int $teamId): void
    {
        if ($this->currencySymbol === null) {
            $companyDetail = Filament::getTenant();
            if ($companyDetail && $companyDetail->country_id) {

                $taxSlab = TaxSlabs::where('country_id', $companyDetail->country_id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $this->currencySymbol = ($taxSlab && !empty($taxSlab->salary_currency)) ? $taxSlab->salary_currency : '';
            } else {
                Log::warning("PayrollCalculationService: CompanyDetail or country not found for Team {$teamId}. Currency symbol set to empty.");
                $this->currencySymbol = '';
            }
        }
    }

    public function getCurrencySymbolForAdmin(int $teamId): string
    {
        if ($this->currencySymbol === null) {
            $this->fetchCompanyCurrencySymbol($teamId);
        }

        return $this->currencySymbol;
    }

    private function fetchBaseSalaryDetails(User $user, ?float $manualBaseSalaryOverride): array
    {
        if ($manualBaseSalaryOverride !== null) {
            $baseSalaryForPeriod = $manualBaseSalaryOverride;
            $originalBaseSalary = $manualBaseSalaryOverride;
        } else {
            $now = Carbon::now();
            $probationEndDate = $user->probation ? Carbon::parse($user->probation) : null;
            $onProbation = $probationEndDate && $now->lt($probationEndDate);
            $baseSalaryForPeriod = $onProbation ? ($user->probation_salary ?? 0.0) : ($user->base_salary ?? 0.0);
            $originalBaseSalary = $baseSalaryForPeriod;
        }
        return [
            'base_salary_for_period' => (float)$baseSalaryForPeriod,
            'original_base_salary' => (float)$originalBaseSalary,
            'salary_currency_from_user' => $user->salary_currency, // This isn't used elsewhere yet but good to have
        ];
    }

    private function calculateWorkingDays(Carbon $start, Carbon $end, User $employee): array
    {
        $teamId = Filament::getTenant()->id;
        if ($teamId === null || !is_int($teamId)) {
            Log::error("PayrollCalculationService::calculateWorkingDays: Invalid admin_id for User ID {$employee->id}. admin_id value: " . print_r($teamId, true));
            throw new \InvalidArgumentException("User with ID {$employee->id} has an invalid or missing admin_id. Cannot calculate working days.");
        }

        $periodStart = $start->copy()->startOfDay();
        $periodEnd = $end->copy()->endOfDay();
        $employeeJoiningDate = $employee->joining_date ? Carbon::parse($employee->joining_date)->startOfDay() : null;

        $holidaysQuery = Holiday::query()
            ->where('team_id', $teamId)
            ->where(function ($query) use ($periodStart, $periodEnd) {
                $query->where(function ($q) use ($periodStart, $periodEnd) {
                    $q->where('starting_date', '>=', $periodStart->toDateString())->where('starting_date', '<=', $periodEnd->toDateString());
                })->orWhere(function ($q) use ($periodStart, $periodEnd) {
                    $q->where('ending_date', '>=', $periodStart->toDateString())->where('ending_date', '<=', $periodEnd->toDateString());
                })->orWhere(function ($q) use ($periodStart, $periodEnd) {
                    $q->where('starting_date', '<=', $periodStart->toDateString())->where('ending_date', '>=', $periodEnd->toDateString());
                });
            })
            ->where(function ($query) use ($employee) {
                $query->where('apply', 'all')
                    ->orWhere(fn($sub) => $sub->where('apply', 'user')->whereJsonContains('users', (string) $employee->id))
                    ->orWhere(fn($sub) => $sub->where('apply', 'shift')->whereJsonContains('shifts', (string) ($employee->assignedShift->shift_id ?? null)))
                    ->orWhere(fn($sub) => $sub->where('apply', 'department')->whereJsonContains('departments', (string) ($employee->assignedDepartment->department_id ?? null)));
            })->get();

        $holidayDates = collect();
        foreach ($holidaysQuery as $holiday) {
            $hStart = Carbon::parse($holiday->starting_date)->max($periodStart);
            $hEnd = Carbon::parse($holiday->ending_date)->min($periodEnd);
            for ($date = $hStart->copy(); $date->lte($hEnd); $date->addDay()) {
                $holidayDates->push($date->toDateString());
            }
        }
        $holidayDates = $holidayDates->unique();
        $workingDaysCount = 0;
        for ($date = $periodStart->copy(); $date->lte($periodEnd); $date->addDay()) {
            if ($employeeJoiningDate && $date->lt($employeeJoiningDate)) continue;
            if (!$holidayDates->contains($date->toDateString())) {
                $workingDaysCount++;
            }
        }
        return ['workingDays' => $workingDaysCount, 'holidayDates' => $holidayDates];
    }

    private function calculateAttendanceSummary(User $user, Carbon $startDate, Carbon $endDate, int $initialWorkingDays): array
    {
        $employeeJoiningDate = $user->joining_date ? Carbon::parse($user->joining_date)->startOfDay() : null;
        $policy = $this->HelperService->policy(Filament::getTenant()->id); // Assuming policy() can accept admin_id

        $allAttendances = $this->HelperService->getAttendanceWithinDateRange($startDate->toDateString(), $endDate->toDateString(), $user->id);
        $leaveBalances = $this->leaveService->getLeaveBalanceForUser($user, $startDate->toDateString(), $endDate->toDateString());

        $summary = [
            'present_days_count' => 0.0,
            'absent_days_count' => 0.0,
            'paid_leave_days_count' => 0.0,
            'unpaid_leave_days_count' => 0.0,
            'total_late_minutes' => 0,
            'total_overtime_minutes' => 0,
            'actual_working_days' => (float)$initialWorkingDays,
        ];

        // Add a null check for $leaveBalances before iterating
        if ($leaveBalances !== null && (is_array($leaveBalances) || $leaveBalances instanceof \Traversable)) {
            foreach ($leaveBalances as $balance) {
                $summary['paid_leave_days_count'] += (float)($balance['used'] ?? 0);
                $summary['unpaid_leave_days_count'] += (float)($balance['unpaid_used'] ?? 0);
            }
        } else {
            Log::warning("PayrollCalculationService: \$leaveBalances is null or not iterable for user {$user->id} in calculateAttendanceSummary.");
        }

        for ($currentDay = $startDate->copy(); $currentDay->lte($endDate); $currentDay->addDay()) {
            if ($employeeJoiningDate && $currentDay->lt($employeeJoiningDate)) continue;
            if ($this->HelperService->checkHoliday($user, $currentDay)) continue;

            $anyApprovedLeaveForDay = Leave::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereDate('starting_date', '<=', $currentDay)
                ->whereDate('ending_date', '>=', $currentDay)
                ->exists();

            if ($anyApprovedLeaveForDay) continue;

            $recordsForDay = $allAttendances->filter(fn($att) => Carbon::parse($att->finger)->isSameDay($currentDay));

            if ($recordsForDay->isNotEmpty()) {
                $sortedFingers = $recordsForDay->pluck('finger')->map(fn($f) => Carbon::parse($f))->sort()->values();
                $isHalfDayByPunch = false;
                $shiftLog = ShiftLog::where('shift_id', $user->shift_id)
                    ->whereDate('created_at', '<=', $currentDay->toDateString())
                    ->orderBy('created_at', 'desc')->first();

                if ($shiftLog && $sortedFingers->count() >= 1) {
                    $firstPunch = $sortedFingers->first()->copy()->setSeconds(0);
                    $lastPunch = $sortedFingers->last()->copy()->setSeconds(0);
                    $halfDayCheckInTime = $shiftLog->half_day_check_in ? Carbon::parse($currentDay->format('Y-m-d') . ' ' . $shiftLog->half_day_check_in)->setSeconds(0) : null;
                    $halfDayCheckOutTime = $shiftLog->half_day_check_out ? Carbon::parse($currentDay->format('Y-m-d') . ' ' . $shiftLog->half_day_check_out)->setSeconds(0) : null;

                    if ($halfDayCheckInTime && $halfDayCheckOutTime) {
                        if ($firstPunch->gt($halfDayCheckInTime) || $lastPunch->lt($halfDayCheckOutTime)) {
                            $isHalfDayByPunch = true;
                        }
                    }
                }

                if ($isHalfDayByPunch) {
                    $summary['present_days_count'] += 0.5;
                    $summary['absent_days_count'] += 0.5;
                } else {
                    $summary['present_days_count'] += 1.0;
                    if ($policy && $policy->late_policy_enabled == 1 && method_exists($this->HelperService, 'calculateLateMin') && $sortedFingers->count() >= 1) {
                        try {
                            $lateMinutes = $this->HelperService->calculateLateMin($sortedFingers, $user, $currentDay);
                            if ($lateMinutes > 0) $summary['total_late_minutes'] += $lateMinutes;
                        } catch (\Throwable $e) {
                            Log::error("Error calculating late minutes for user {$user->id} on {$currentDay->toDateString()} in service: " . $e->getMessage());
                        }
                    }
                }

                if ($policy && $policy->overtime_policy_enabled == 1 && method_exists($this->HelperService, 'calculateOvertimePerDay') && $sortedFingers->count() >= 2) {
                    try {
                        $overtime = $this->HelperService->calculateOvertimePerDay($sortedFingers, $user, $currentDay);
                        if ($overtime > 0) $summary['total_overtime_minutes'] += $overtime;
                    } catch (\Throwable $e) {
                        Log::error("Error calculating overtime for user {$user->id} on {$currentDay->toDateString()} in service: " . $e->getMessage());
                    }
                }
            } else {
                $summary['absent_days_count'] += 1.0;
            }
        }
        return $summary;
    }

    private function calculateFinancialRates(User $user, float $baseSalary, int $workingDaysInPeriod): array
    {
        $rates = ['per_day_rate' => null, 'per_minute_rate' => null];
        if ($workingDaysInPeriod <= 0 || $baseSalary <= 0) return $rates;

        $rates['per_day_rate'] = $baseSalary / $workingDaysInPeriod;
        $totalWorkingMinutesPerDay = 0;

        if (in_array($user->attendance_type, ['offsite', 'hybrid']) && $user->hours_required) {
            $totalWorkingMinutesPerDay = $user->hours_required * 60;
        } else {
            $latestShiftLog = $user->assignedShift ? ShiftLog::where('shift_id', $user->assignedShift->shift_id)
                ->orderBy('created_at', 'desc')->first() : null;

            if ($latestShiftLog) {
                try {
                    $shiftStart = Carbon::parse($latestShiftLog->starting_time);
                    $shiftEnd = Carbon::parse($latestShiftLog->ending_time);
                    if ($shiftStart->gt($shiftEnd)) $shiftEnd->addDay();
                    $totalShiftMinutes = $shiftStart->diffInMinutes($shiftEnd);
                    $breakMinutes = 0;
                    if ($latestShiftLog->break_start && $latestShiftLog->break_end) {
                        $breakStart = Carbon::parse($latestShiftLog->break_start);
                        $breakEnd = Carbon::parse($latestShiftLog->break_end);
                        if ($breakStart->gt($breakEnd)) $breakEnd->addDay();
                        $breakMinutes = $breakStart->diffInMinutes($breakEnd);
                    }
                    $totalWorkingMinutesPerDay = max(0, $totalShiftMinutes - $breakMinutes);
                } catch (\Exception $e) {
                    Log::error("Error calculating working minutes for shift ID {$user->shift_id} from ShiftLog in service: " . $e->getMessage());
                }
            }
        }

        if ($totalWorkingMinutesPerDay > 0 && $rates['per_day_rate'] !== null) {
            $rates['per_minute_rate'] = $rates['per_day_rate'] / $totalWorkingMinutesPerDay;
        }
        return $rates;
    }

    private function calculateAttendanceFinancialImpacts(
        int $totalLateMinutes,
        float $totalAbsentAndUnpaidLeaveDays,
        int $totalOvertimeMinutes,
        ?float $perMinuteRate,
        ?float $perDayRate
    ): array {
        $perMinuteRateVal = $perMinuteRate ?? 0.0;
        $perDayRateVal = $perDayRate ?? 0.0;
        return [
            'late_deduction_amount' => $totalLateMinutes * $perMinuteRateVal,
            'absent_deduction_amount' => $totalAbsentAndUnpaidLeaveDays * $perDayRateVal,
            'overtime_earning_amount' => $totalOvertimeMinutes * $perMinuteRateVal,
        ];
    }

    private function processSalaryComponents(
        User $user,
        float $baseSalary,
        array $customEarningsInput,
        array $customDeductionsInput,
        Carbon $periodStartDate
    ): array {
        $teamId = Filament::getTenant()->id;
        if ($teamId === null || !is_int($teamId)) {
            Log::error("PayrollCalculationService::processSalaryComponents: Invalid admin_id for User ID {$user->id}. admin_id value: " . print_r($teamId, true));
            throw new \InvalidArgumentException("User with ID {$user->id} has an invalid or missing admin_id. Cannot process salary components.");
        }

        // 1. Fetch the most recent previous payroll for this user.
        $lastPayroll = Payroll::where('user_id', $user->id)
            ->orderBy('date_range_start', 'desc')
            ->first();

        // 2. Prepare previous component data for easy lookup, keyed by component ID.
        $previousEarnings = collect();
        $previousDeductions = collect();
        if ($lastPayroll) {
            $previousEarnings = collect($lastPayroll->earnings_data['custom_earnings_applied'] ?? [])
                ->keyBy('id');
            $previousDeductions = collect($lastPayroll->deductions_data['custom_deductions_applied'] ?? [])
                ->keyBy('id');
        }

        $previouslyAppliedOneTimeDeductionIds = Payroll::where('user_id', $user->id)
            ->where('date_range_start', '!=', $periodStartDate->toDateString())
            ->get()
            ->flatMap(fn($payroll) => $payroll->applied_one_time_deductions ?? [])
            ->filter()
            ->unique()->toArray();

        $components = SalaryComponent::where('team_id', $teamId)
            ->where('is_active', true)
            ->get();

        // Initialize structured arrays for final database storage
        $finalCustomEarningsApplied = [];
        $finalPredefinedEarnings = [];
        $finalCustomDeductionsApplied = [];
        $finalPredefinedDeductions = [];
        $appliedOneTimeDeductionIdsThisRun = [];
        $processedComponentTitles = ['earning' => [], 'deduction' => []];

        $totals = ['total_taxable_earnings' => 0.0, 'total_non_taxable_earnings' => 0.0, 'total_taxable_deductions' => 0.0, 'total_non_taxable_deductions' => 0.0];

        // Process custom earnings (from input)
        foreach ($customEarningsInput as $earning) {
            $calculatedAmount = round($earning['type'] === 'percentage' ? ($baseSalary * ((float)($earning['amount'] ?? 0) / 100)) : (float)($earning['amount'] ?? 0));
            $finalCustomEarningsApplied[] = [
                'title' => $earning['title'],
                'amount_input' => (float)($earning['amount'] ?? 0),
                'type' => $earning['type'],
                'tax_status' => $earning['tax_status'],
                'calculated_amount' => $calculatedAmount,
                'id' => null // Ad-hoc components don't have a component ID
            ];
            if ($earning['tax_status'] === 'taxable') $totals['total_taxable_earnings'] += $calculatedAmount;
            else $totals['total_non_taxable_earnings'] += $calculatedAmount;
            $processedComponentTitles['earning'][] = $earning['title'];
        }

        // Process custom deductions (from input)
        foreach ($customDeductionsInput as $deduction) {
            $calculatedAmount = $deduction['type'] === 'percentage' ? ($baseSalary * ((float)($deduction['amount'] ?? 0) / 100)) : (float)($deduction['amount'] ?? 0);
            $isOneTime = $deduction['is_one_time_deduction'] ?? false;
            $finalCustomDeductionsApplied[] = [
                'title' => $deduction['title'],
                'amount_input' => (float)($deduction['amount'] ?? 0),
                'type' => $deduction['type'],
                'tax_status' => 'non-taxable',
                'calculated_amount' => $calculatedAmount,
                'id' => null, // Ad-hoc components don't have a component ID
                'is_one_time_deduction' => $isOneTime
            ];
            $totals['total_taxable_deductions'] += $calculatedAmount;
            $processedComponentTitles['deduction'][] = $deduction['title'];
        }

        // Funds Culculation
        $user_funds = $user->funds()
            ->wherePivot('team_id', Filament::getTenant()->id)
            ->get();
        $finalFundApplied = [];
        foreach ($user_funds as $fund) {
            if ($baseSalary > 0) {
                $matchedBracket = collect($fund->brackets)->first(function ($bracket) use ($baseSalary) {
                    return $baseSalary >= $bracket['min_annual_salary'] && $baseSalary <= $bracket['max_annual_salary'];
                });
                if ($matchedBracket) {
                    if ($matchedBracket['type'] === 'percentage') {
                        $amount = ($baseSalary * $matchedBracket['percentage']) / 100;
                    } elseif ($matchedBracket['type'] === 'fixed_amount') {
                        $amount = $matchedBracket['fixed_amount'];
                    } else {
                        $amount = 0;
                    }
                    $finalFundApplied[] = [
                        'title' => $fund->name,
                        'amount_input' => $amount,
                        'type' => $matchedBracket['type'],
                        'tax_status' => 'non-taxable',
                        'calculated_amount' => $amount,
                        'id' => $fund->id,
                        'is_one_time_deduction' => false
                    ];
                    $totals['total_non_taxable_deductions'] += $amount;
                }
            }
        }
        foreach ($components as $component) {
            if (in_array($component->title, $processedComponentTitles[$component->component_type])) {
                continue;
            }

            // 3. NEW LOGIC: Determine which amount to use (from previous payroll or default)
            $amountToUse = (float)$component->amount; // Start with the default amount
            $isEarning = $component->component_type === 'earning';

            if ($isEarning && $previousEarnings->has($component->id)) {
                // Component found in previous earnings, so use its 'amount_input'.
                $amountToUse = (float)($previousEarnings->get($component->id)['amount_input'] ?? $component->amount);
            } elseif (!$isEarning && $previousDeductions->has($component->id)) {
                // Component found in previous deductions, so use its 'amount_input'.
                $amountToUse = (float)($previousDeductions->get($component->id)['amount_input'] ?? $component->amount);
            }

            // Use the determined amount for calculation
            $calculatedAmount = $component->value_type === 'percentage' ? ($baseSalary * ($amountToUse / 100)) : $amountToUse;

            if ($component->component_type === 'earning') {
                $finalPredefinedEarnings[] = [
                    'title' => $component->title,
                    'amount_input' => $amountToUse,
                    'type' => $component->value_type,
                    'tax_status' => $component->tax_status,
                    'calculated_amount' => $calculatedAmount,
                    'id' => $component->id
                ];
                if ($component->tax_status === 'taxable') $totals['total_taxable_earnings'] += $calculatedAmount;
                else $totals['total_non_taxable_earnings'] += $calculatedAmount;
            } else { // Deduction
                if ($component->is_one_time_deduction) {
                    if (in_array($component->id, $previouslyAppliedOneTimeDeductionIds)) {
                        continue;
                    }
                    $appliedOneTimeDeductionIdsThisRun[] = $component->id;
                }
                $finalPredefinedDeductions[] = [
                    'title' => $component->title,
                    'amount_input' => $amountToUse, // Store the amount that was used
                    'type' => $component->value_type,
                    'tax_status' => $component->tax_status,
                    'calculated_amount' => $calculatedAmount,
                    'id' => $component->id,
                    'is_one_time_deduction' => (bool)$component->is_one_time_deduction
                ];
                $totals['total_taxable_deductions'] += $calculatedAmount;
            }
        }

        return array_merge($totals, [
            'final_earnings_for_db' => [
                'custom_earnings_applied' => array_merge($finalCustomEarningsApplied, $finalPredefinedEarnings),
                'ad_hoc_earnings' => [],
                'attendance_earnings' => [],
            ],
            'final_deductions_for_db' => [
                'custom_deductions_applied' => array_merge($finalCustomDeductionsApplied, $finalPredefinedDeductions),
                'ad_hoc_deductions' => []
            ],
            'final_fund_deductions_for_db' => $finalFundApplied,
            'applied_one_time_deduction_ids' => array_unique($appliedOneTimeDeductionIdsThisRun),
        ]);
    }

    private function calculateTax(
        User $user,
        int $teamId,
        float $currentPeriodBaseSalary,
        float $totalTaxableEarningsForCurrentPeriod,
        float $totalTaxableDeductionsForCurrentPeriod,
        int $effectiveMonthIndicatorForCurrentRun,
        Carbon $currentRunStartDate
    ): array {
        $monthlyTaxableBaseForCurrentPeriod = $currentPeriodBaseSalary + $totalTaxableEarningsForCurrentPeriod - $totalTaxableDeductionsForCurrentPeriod;

        $companyDetails = Team::where('id', $teamId)->first();
        $companyCountry = $companyDetails ? $companyDetails->country_id : null;

        if (!$companyCountry) {
            Log::warning("Company country not set for admin ID: {$teamId}");
            return [
                'annual_taxable_salary' => round($monthlyTaxableBaseForCurrentPeriod),
                'monthly_taxable_base' => round($monthlyTaxableBaseForCurrentPeriod),
                'total_annual_tax_calculated' => 0.0,
                'monthly_tax_calculated' => 0.0,
                'tax_slabs_country' => null,
                'months_remaining_in_fy' => 1,
            ];
        }

        $taxSlabsRecord = \App\Models\TaxSlabs::where('country_id', $companyCountry)
            ->where('financial_year_start', '<=', $currentRunStartDate->toDateString())
            ->where('financial_year_end', '>=', $currentRunStartDate->toDateString())
            ->first();

        $slabsData = null;
        $fyStartDate = null;
        $fyEndDate = null;

        if ($taxSlabsRecord) {
            $slabsData = $taxSlabsRecord->slabs_data;
            $fyStartDate = Carbon::parse($taxSlabsRecord->financial_year_start);
            $fyEndDate = Carbon::parse($taxSlabsRecord->financial_year_end);
        } else {
            Log::warning("No applicable tax slabs found for country: {$companyCountry} for admin {$teamId} for date {$currentRunStartDate->toDateString()}");
            return [
                'annual_taxable_salary' => round($monthlyTaxableBaseForCurrentPeriod),
                'monthly_taxable_base' => round($monthlyTaxableBaseForCurrentPeriod),
                'total_annual_tax_calculated' => 0.0,
                'monthly_tax_calculated' => 0.0,
                'tax_slabs_country' => $companyCountry,
                'months_remaining_in_fy' => 1,
            ];
        }

        $monthsRemainingInFY = $this->calculateMonthsRemainingInFY(
            $currentRunStartDate->month,
            $currentRunStartDate->day,
            $fyEndDate->month,
            $fyEndDate->day
        );

        $currentFYStart = $fyStartDate;

        $previousMonthsPayrollQuery = Payroll::where('user_id', $user->id)
            ->where('date_range_start', '>=', $currentFYStart->toDateString())
            ->where('date_range_start', '<', $currentRunStartDate->toDateString());

        $previousMonthsTaxableBaseSum = (clone $previousMonthsPayrollQuery)
            ->get()
            ->sum(function ($payroll) {
                return (float) ($payroll->tax_data['monthly_taxable_base'] ?? 0);
            });

        $multiplier = 1;
        $latestPreviousPayroll = (clone $previousMonthsPayrollQuery)
            ->orderByDesc('date_range_start')
            ->first();

        if ($latestPreviousPayroll) {
            $previousMonthsRemainingInFY = (float)($latestPreviousPayroll->tax_data['months_remaining_in_fy'] ?? 0);
            $multiplier = max(1, $previousMonthsRemainingInFY - 1);
        } else {
            $multiplier = $monthsRemainingInFY;
        }

        $projectedAnnualSalary = ($monthlyTaxableBaseForCurrentPeriod * $multiplier) + $previousMonthsTaxableBaseSum;

        $totalAnnualTax = 0.0;
        $monthlyTaxCalculated = 0.0;

        $taxSlabs = collect($slabsData)->sortBy('min_annual_salary')->values();
        $previousThreshold = 0.0;
        $previousPercentage = 0.0;
        $accumulatedAdditionalTax = 0.0;

        foreach ($taxSlabs as $index => $slabData) {
            $currentThreshold = (float)($slabData['min_annual_salary'] ?? 0.0);
            $currentPercentage = (float)($slabData['tax_percentage'] ?? 0.0);
            $currentAdditionalTax = (float)($slabData['additional_tax'] ?? 0.0);

            if ($projectedAnnualSalary > $previousThreshold) {
                $incomeInPreviousRange = min($projectedAnnualSalary, $currentThreshold) - $previousThreshold;
                if ($incomeInPreviousRange < 0) $incomeInPreviousRange = 0;
                if ($previousPercentage > 0) $totalAnnualTax += $incomeInPreviousRange * ($previousPercentage / 100);
            }
            if ($projectedAnnualSalary >= $currentThreshold) $accumulatedAdditionalTax += $currentAdditionalTax;

            if ($index === $taxSlabs->count() - 1 && $projectedAnnualSalary > $currentThreshold) {
                $totalAnnualTax += ($projectedAnnualSalary - $currentThreshold) * ($currentPercentage / 100);
            } elseif ($projectedAnnualSalary <= $currentThreshold) {
                break;
            }
            $previousThreshold = $currentThreshold;
            $previousPercentage = $currentPercentage;
        }

        $previousMonthsTaxPaidSum = (clone $previousMonthsPayrollQuery)
            ->get()
            ->sum(function ($payroll) {
                return (float) ($payroll->tax_data['monthly_tax_calculated'] ?? 0);
            });

        $remainingTaxToBePaid = $totalAnnualTax - $previousMonthsTaxPaidSum;

        $monthlyTaxCalculated = $remainingTaxToBePaid > 0 ? round($remainingTaxToBePaid / $monthsRemainingInFY) : 0.0;
        $monthlyTaxCalculated = max(0, $monthlyTaxCalculated);

        return [
            'annual_taxable_salary' => round($projectedAnnualSalary),
            'monthly_taxable_base' => round($monthlyTaxableBaseForCurrentPeriod),
            'total_annual_tax_calculated' => round($totalAnnualTax),
            'monthly_tax_calculated' => round($monthlyTaxCalculated),
            'tax_slabs_country' => $companyCountry,
            'months_remaining_in_fy' => $monthsRemainingInFY,
        ];
    }

    protected function calculateMonthsRemainingInFY(int $currentMonth, int $currentDay, int $fyEndMonth, int $fyEndDay): int
    {
        $currentDate = Carbon::create(null, $currentMonth, $currentDay);
        $fyEndDate = Carbon::create(null, $fyEndMonth, $fyEndDay);

        if ($currentDate->gt($fyEndDate)) {
            $fyEndDate->addYear();
        }

        $monthsRemaining = $currentDate->diffInMonths($fyEndDate, false);

        if ($currentDate->gt($fyEndDate) || ($currentDate->month === $fyEndDate->month && $currentDate->day > $fyEndDate->day)) {
            return 0;
        }

        return max(1, $monthsRemaining + 1);
    }

    protected function determineFinancialYearStart(Carbon $date, int $fyStartMonth, int $fyStartDay): Carbon
    {
        $fyStart = Carbon::create($date->year, $fyStartMonth, $fyStartDay)->startOfDay();
        if ($date->lt($fyStart)) {
            $fyStart->subYear();
        }
        return $fyStart;
    }

    protected function determineFinancialYearEnd(Carbon $date, int $fyEndMonth, int $fyEndDay): Carbon
    {
        $fyEnd = Carbon::create($date->year, $fyEndMonth, $fyEndDay)->endOfDay();
        if ($date->gt($fyEnd)) {
            $fyEnd->addYear();
        }
        return $fyEnd;
    }

    public function recalculateEmployeePayrollData(
        Payroll $payroll,
        float $originalBaseSalary,
        array $predefinedEarningsInput,
        array $predefinedDeductionsInput,
        array $adHocEarningsInput,
        array $adHocDeductionsInput,
        bool $applyIncrementForThisRun,
        string $incrementType,
        ?float $incrementValue,
        bool $deductLatePenalties,
        bool $deductAbsentPenalties,
        bool $applyOvertimeEarnings
    ): array {
        $user = $payroll->user;
        $periodStartDate = Carbon::parse($payroll->date_range_start);

        $previouslyAppliedOneTimeDeductionIds = Payroll::where('user_id', $user->id)
            ->where('id', '!=', $payroll->id)
            ->get()
            ->flatMap(fn($p) => $p->applied_one_time_deductions ?? [])
            ->filter()
            ->unique()
            ->toArray();

        $appliedOneTimeDeductionIdsThisRun = [];

        $baseSalary = $originalBaseSalary;
        $appliedIncrementAmount = 0;

        if ($applyIncrementForThisRun && $incrementValue !== null && $incrementValue > 0) {
            $appliedIncrementAmount = ($incrementType === 'percentage') ? ($baseSalary * ($incrementValue / 100)) : $incrementValue;
            $baseSalary += $appliedIncrementAmount;
        }

        $customEarningsApplied = [];
        $adHocEarningsCalculated = [];
        $customDeductionsApplied = [];
        $adHocDeductionsCalculated = [];

        if ($this->currencySymbol === null) {
            $this->fetchCompanyCurrencySymbol(Filament::getTenant()->id);
        }

        $totalCustomTaxableEarnings = 0.0;
        $totalCustomNonTaxableEarnings = 0.0;
        $totalAdHocTaxableEarnings = 0.0;
        $totalAdHocNonTaxableEarnings = 0.0;

        foreach ($predefinedEarningsInput as $earningInput) {
            $component = SalaryComponent::find($earningInput['id']);
            if ($component) {
                $inputAmountForCalculation = (float)($earningInput['amount_input'] ?? $component->amount);
                $calculatedAmount = round($component->value_type === 'percentage'
                    ? ($baseSalary * ($inputAmountForCalculation / 100))
                    : $inputAmountForCalculation);

                $customEarningsApplied[] = [
                    'id' => $component->id,
                    'name' => $component->name,
                    'title' => $component->title,
                    'type' => $component->component_type,
                    'value_type' => $component->value_type,
                    'tax_status' => $component->tax_status,
                    'amount_input' => $inputAmountForCalculation,
                    'calculated_amount' => $calculatedAmount,
                    'is_one_time_deduction' => (bool)$component->is_one_time_deduction,
                ];
                if ($component->tax_status === 'taxable') {
                    $totalCustomTaxableEarnings += $calculatedAmount;
                } else {
                    $totalCustomNonTaxableEarnings += $calculatedAmount;
                }
            }
        }
        foreach ($adHocEarningsInput as $adHocEarning) {
            $amount = (float)($adHocEarning['amount_input'] ?? 0);
            $adHocEarning['value_type'] = $adHocEarning['value_type'] ?? 'number';
            $calculatedAmount = round($adHocEarning['value_type'] === 'percentage' ? ($baseSalary * ($amount / 100)) : $amount);
            $adHocEarningsCalculated[] = [
                'id' => $adHocEarning['id'] ?? uniqid('adhoc_earning_'),
                'name' => Str::slug($adHocEarning['title']),
                'title' => $adHocEarning['title'],
                'type' => 'earning',
                'value_type' => $adHocEarning['value_type'],
                'tax_status' => $adHocEarning['tax_status'],
                'amount_input' => $amount,
                'calculated_amount' => $calculatedAmount,
                'is_one_time_deduction' => false,
            ];
            $totalAdHocTaxableEarnings += $calculatedAmount;
        }


        $totalCustomTaxableDeductions = 0.0;
        $totalCustomNonTaxableDeductions = 0.0;
        $totalAdHocTaxableDeductions = 0.0;
        $totalAdHocNonTaxableDeductions = 0.0;

        foreach ($predefinedDeductionsInput as $deductionInput) {
            $component = SalaryComponent::find($deductionInput['id']);
            if ($component) {
                if ($component->is_one_time_deduction) {
                    if (in_array($component->id, $previouslyAppliedOneTimeDeductionIds)) {
                        continue;
                    }
                    $appliedOneTimeDeductionIdsThisRun[] = $component->id;
                }

                $inputAmountForCalculation = (float)($deductionInput['amount_input'] ?? $component->amount);
                $calculatedAmount = round($component->value_type === 'percentage'
                    ? ($baseSalary * ($inputAmountForCalculation / 100))
                    : $inputAmountForCalculation);
                $customDeductionsApplied[] = [
                    'id' => $component->id,
                    'name' => $component->name,
                    'title' => $component->title,
                    'type' => $component->component_type,
                    'value_type' => $component->value_type,
                    'tax_status' => $component->tax_status,
                    'amount_input' => $inputAmountForCalculation,
                    'calculated_amount' => $calculatedAmount,
                    'is_one_time_deduction' => (bool)$component->is_one_time_deduction,
                ];
                if ($component->tax_status === 'taxable') {
                    $totalCustomTaxableDeductions += $calculatedAmount;
                } else {
                    $totalCustomNonTaxableDeductions += $calculatedAmount;
                }
            }
        }

        foreach ($adHocDeductionsInput as $adHocDeduction) {
            $amount = (float)($adHocDeduction['amount_input'] ?? 0);
            $calculatedAmount = $adHocDeduction['value_type'] === 'percentage' ? ($baseSalary * ($amount / 100)) : $amount;
            $adHocDeductionsCalculated[] = [
                'id' => uniqid('adhoc_deduction_'),
                'name' => Str::slug($adHocDeduction['title']),
                'title' => $adHocDeduction['title'],
                'type' => 'deduction',
                'value_type' => $adHocDeduction['value_type'],
                'tax_status' => 'non-taxable',
                'amount_input' => $amount,
                'calculated_amount' => $calculatedAmount,
                'is_one_time_deduction' => true,
            ];
            $totalAdHocNonTaxableDeductions += $calculatedAmount;
        }
        $user_funds = $user->funds()
            ->wherePivot('team_id', Filament::getTenant()->id)
            ->get();
        $finalFundApplied = [];
        foreach ($user_funds as $fund) {
            if ($baseSalary > 0) {
                $matchedBracket = collect($fund->brackets)->first(function ($bracket) use ($baseSalary) {
                    return $baseSalary >= $bracket['min_annual_salary'] && $baseSalary <= $bracket['max_annual_salary'];
                });
                if ($matchedBracket) {
                    if ($matchedBracket['type'] === 'percentage') {
                        $amount = ($baseSalary * $matchedBracket['percentage']) / 100;
                    } elseif ($matchedBracket['type'] === 'fixed_amount') {
                        $amount = $matchedBracket['fixed_amount'];
                    } else {
                        $amount = 0;
                    }
                    $finalFundApplied[] = [
                        'title' => $fund->name,
                        'amount_input' => $amount,
                        'type' => $matchedBracket['type'],
                        'tax_status' => 'non-taxable',
                        'calculated_amount' => $amount,
                        'id' => $fund->id,
                        'is_one_time_deduction' => false
                    ];
                    $totalAdHocNonTaxableDeductions += $amount;
                }
            }
        }
        $finalEarningsForDb = ['custom_earnings_applied' => $customEarningsApplied, 'ad_hoc_earnings' => $adHocEarningsCalculated,];
        $finalDeductionsForDb = ['custom_deductions_applied' => $customDeductionsApplied, 'ad_hoc_deductions' => $adHocDeductionsCalculated,];

        $originalAttendanceData = $payroll->attendance_data;
        $attendanceFinancialImpacts = $this->calculateAttendanceFinancialImpacts(
            $originalAttendanceData['total_late_minutes'] ?? 0,
            ($originalAttendanceData['absent_days_count'] ?? 0) + ($originalAttendanceData['unpaid_leave_days_count'] ?? 0),
            $originalAttendanceData['total_overtime_minutes'] ?? 0,
            $originalAttendanceData['per_minute_rate_used'] ?? 0,
            $originalAttendanceData['per_day_rate_used'] ?? 0
        );
        $taxableAttendanceEarnings = 0.0;
        $nonTaxableAttendanceEarnings = 0.0;
        $taxableAttendancePenalties = 0.0;
        $nonTaxableAttendancePenalties = 0.0;

        $overtimeAmount = round($attendanceFinancialImpacts['overtime_earning_amount'] ?? 0.0);
        if ($applyOvertimeEarnings) {
            $taxableAttendanceEarnings += $overtimeAmount;
        }

        if ($deductLatePenalties) {
            $nonTaxableAttendancePenalties += $attendanceFinancialImpacts['late_deduction_amount'];
        }

        if ($deductAbsentPenalties) {
            $nonTaxableAttendancePenalties += $attendanceFinancialImpacts['absent_deduction_amount'];
        }

        $totalTaxableEarnings = $totalCustomTaxableEarnings + $totalAdHocTaxableEarnings + $taxableAttendanceEarnings;
        $totalNonTaxableEarnings = $totalCustomNonTaxableEarnings + $totalAdHocNonTaxableEarnings + $nonTaxableAttendanceEarnings;
        $totalTaxableDeductions = $totalCustomTaxableDeductions + $totalAdHocTaxableDeductions + $taxableAttendancePenalties;
        $totalNonTaxableDeductions = $totalCustomNonTaxableDeductions + $totalAdHocNonTaxableDeductions + $nonTaxableAttendancePenalties;

        $updatedAttendanceData = $originalAttendanceData;
        $updatedAttendanceData['late_minutes_deduction_amount'] = $attendanceFinancialImpacts['late_deduction_amount'];
        $updatedAttendanceData['absent_deduction_amount'] = $attendanceFinancialImpacts['absent_deduction_amount'];
        $updatedAttendanceData['overtime_earning_amount'] = $overtimeAmount;
        $updatedAttendanceData['deduct_late_penalties'] = $deductLatePenalties;
        $updatedAttendanceData['deduct_absent_penalties'] = $deductAbsentPenalties;
        $updatedAttendanceData['apply_overtime_earnings'] = $applyOvertimeEarnings;

        $taxData = $this->calculateTax(
            $user,
            Filament::getTenant()->id,
            $baseSalary,
            $totalTaxableEarnings,
            $totalTaxableDeductions,
            $payroll->month_indicator,
            $periodStartDate
        );

        $netPayableSalary = $baseSalary
            + $totalTaxableEarnings
            + $totalNonTaxableEarnings
            - $totalTaxableDeductions
            - $totalNonTaxableDeductions
            - $taxData['monthly_tax_calculated'];

        return [
            'base_salary' => round($baseSalary),
            'applied_increment_amount' => round($appliedIncrementAmount),
            'earnings_data' => $finalEarningsForDb,
            'deductions_data' => $finalDeductionsForDb,
            'attendance_data' => $updatedAttendanceData,
            'tax_data' => $taxData,
            'net_payable_salary' => $netPayableSalary,
            'original_base_salary_before_run_increment' => round($originalBaseSalary),
            'deduct_late_penalties' => $deductLatePenalties,
            'deduct_absent_penalties' => $deductAbsentPenalties,
            'apply_overtime_earnings' => $applyOvertimeEarnings,
            'currency_symbol' => $this->currencySymbol,
            'status' => $payroll->status,
            'payment_mode' => $payroll->payment_mode,
            'other_payment_mode' => $payroll->other_payment_mode,
            'applied_one_time_deductions' => array_unique($appliedOneTimeDeductionIdsThisRun),
        ];
    }
}
