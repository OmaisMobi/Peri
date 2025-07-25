<?php

namespace App\Imports;

use App\Models\PayRun;
use App\Models\Payroll;
use App\Models\User;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;


class PayrunWithPayrollImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $sheetData = $this->getSheetData($rows);
        $teamId = Filament::getTenant()->id;
        foreach ($sheetData as $data) {
            $totalTax = 0;
            $user_id = $data['user_detail']['user_id'] ?? null;
            if (!$user_id) {
                continue;
            }
            
            $user = Filament::getTenant()->users()->where('id', $user_id)->get();
            if (!$user) {
                continue;
            };
            $month = (int)$data['duration']['month'] ?? null;
            $year = (int)$data['duration']['year'] ?? null;
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

            if (!$month || !$year) {
                continue;
            }
            $payrunKey = "{$teamId}-{$month}-{$year}";
            $payrun = PayRun::firstOrCreate([
                'team_id' => $teamId,
                'month' => $month,
                'year' => $year,
            ], [
                'pay_period_start_date' => $startDate,
                'pay_period_end_date' => $endDate,
                'status' => 'draft',
            ]);
            $payrunCache[$payrunKey] = $payrun;
            $taxSlabsRecord = \App\Models\TaxSlabs::where('country_id', Filament::getTenant()->country_id)
                ->where('financial_year_start', '<=', $startDate->toDateString())
                ->where('financial_year_end', '>=', $startDate->toDateString())
                ->first();
            $fyEndDate = Carbon::parse($taxSlabsRecord->financial_year_end);
            $monthsRemainingInFY = $this->calculateMonthsRemainingInFY(
                $startDate->month,
                $startDate->day,
                $fyEndDate->month,
                $fyEndDate->day
            );
            $PayrollData = [
                'team_id' => $teamId,
                'user_id' => $user_id,
                'pay_run_id' => $payrun->id,
                'date_range_start' => $startDate,
                'date_range_end' => $endDate,
                'month_indicator' => $month,
                'base_salary' => (int)$data['salary']['base_salary'] ?? 0.00,
                'earnings_data' => [
                    "ad_hoc_earnings" => [],
                    "attendance_earnings" => [],
                    "custom_earnings_applied" => []
                ],
                'deductions_data' => [
                    "ad_hoc_deductions" => [],
                    "custom_deductions_applied" => []
                ],
                'applied_one_time_deductions' => [],
                'attendance_data' => [
                    "total_days" => $daysInMonth,
                    "working_days" => (int)$data['duration']['working_days'] ?? $daysInMonth,
                    "absent_days_count" => 0,
                    "per_day_rate_used" => (int)$data['salary']['base_salary'] / $daysInMonth,
                    "present_days_count" => (int)$data['duration']['working_days'] ?? $daysInMonth,
                    "total_late_minutes" => 0,
                    "actual_working_days" => (int)$data['duration']['working_days'] ?? $daysInMonth,
                    "per_minute_rate_used" => null,
                    "paid_leave_days_count" => 0,
                    "total_overtime_minutes" => 0,
                    "absent_deduction_amount" => 0,
                    "overtime_earning_amount" => 0,
                    "unpaid_leave_days_count" => 0,
                    "late_minutes_deduction_amount" => 0
                ],
                'apply_overtime_earnings' =>  0,
                'deduct_late_penalties' => 0,
                'deduct_absent_penalties' => 0,
                'tax_data' => [
                        "tax_slabs_country"=> Filament::getTenant()->country_id,
                        "monthly_taxable_base"=> (int)$data['salary']['base_salary'],
                        "annual_taxable_salary"=> (int)$data['salary']['base_salary'] * 12,
                        "monthly_tax_calculated"=> 0,
                        "months_remaining_in_fy"=> $monthsRemainingInFY,
                        "total_annual_tax_calculated"=> 0
                ],
                'net_payable_salary' => $data['salary']['net_payable_salary'] ?? 0.00,
                'payment_mode' => 'bank',
                'other_payment_mode' => [],
                'applied_increment_amount' => 0.00,
                'status' => 0,
            ];
            foreach ($data['tax'] as $tax) {
                if ($tax['amount_input'] > 0) {
                   $totalTax += $tax['amount_input'];
                }
            }
            $PayrollData['tax_data']['monthly_tax_calculated'] = $totalTax;
            foreach ($data['deductions'] as $deduction) {
                if ($deduction['amount_input'] > 0) {
                    $PayrollData['deductions_data']['ad_hoc_deductions'][] =
                        [
                            "id" => "adhoc_deduction_" . uniqid(),
                            "name" => $deduction['name'],
                            "type" => "deduction",
                            "title" => $deduction['name'],
                            "tax_status" => "non-taxable",
                            "value_type" => "number",
                            "amount_input" => $deduction['amount_input'],
                            "calculated_amount" => $deduction['amount_input'],
                            "is_one_time_deduction" => true
                        ];
                }
            }

            foreach ($data['non-taxable-earnings'] as $nonTaxableearning) {
                if ($nonTaxableearning['amount_input'] > 0) {
                    $PayrollData['earnings_data']['ad_hoc_earnings'][] =
                        [
                            "id" => "adhoc_earning_" . uniqid(),
                            "name" => $nonTaxableearning['name'],
                            "type" => "earning",
                            "title" => $nonTaxableearning['name'],
                            "tax_status" => "non-taxable",
                            "value_type" => "number",
                            "amount_input" => $nonTaxableearning['amount_input'],
                            "calculated_amount" => $nonTaxableearning['amount_input'],
                            "is_one_time_deduction" => false
                        ];
                }
            }
            foreach ($data['taxable-earnings'] as $taxableEarning) {
                if ($taxableEarning['amount_input'] > 0) {
                    $PayrollData['earnings_data']['ad_hoc_earnings'][] =
                        [
                            "id" => "adhoc_earning_" . uniqid(),
                            "name" => $taxableEarning['name'],
                            "type" => "earning",
                            "title" => $taxableEarning['name'],
                            "tax_status" => "taxable",
                            "value_type" => "number",
                            "amount_input" => $taxableEarning['amount_input'],
                            "calculated_amount" => $taxableEarning['amount_input'],
                            "is_one_time_deduction" => false
                        ];
                }
            }
            foreach ($data['non-taxable-earnings'] as $nontaxableEarning) {
                if ($nontaxableEarning['amount_input'] > 0) {
                    $PayrollData['earnings_data']['ad_hoc_earnings'][] =
                        [
                            "id" => "adhoc_earning_" . uniqid(),
                            "name" => $nontaxableEarning['name'],
                            "type" => "earning",
                            "title" => $nontaxableEarning['name'],
                            "tax_status" => "taxable",
                            "value_type" => "number",
                            "amount_input" => $nontaxableEarning['amount_input'],
                            "calculated_amount" => $nontaxableEarning['amount_input'],
                            "is_one_time_deduction" => false
                        ];
                }
            }
            Payroll::create($PayrollData);
        }
    }
    private function getSheetData(Collection $rows)
    {
        $rowsArray = $rows->toArray();

        $groupHeadings = $rowsArray[0];
        $fields = $rowsArray[1];
        $dataRows = array_slice($rowsArray, 2);

        $finalData = [];

        foreach ($dataRows as $row) {
            $data = [];

            foreach ($fields as $index => $field) {
                $category = $groupHeadings[$index];
                $value = $row[$index];

                if ($category === null || $field === null) {
                    continue;
                }

                $category = strtolower(trim($category));

                if (!in_array($category, ['tax','duration', 'user_detail', 'deductions', 'taxable-earnings', 'non-taxable-earnings', 'salary'])) {
                    continue;
                }

                if (in_array($category, ['tax', 'deductions', 'taxable-earnings', 'non-taxable-earnings'])) {
                    if (!is_null($value) && $value !== '') {
                        $data[$category][$field] = [
                            'name' => $field,
                            'amount_input' => (float)$value,
                        ];
                    }
                } else {
                    if (!is_null($value) && $value !== '') {
                        $data[$category][$field] = $value;
                    }
                }
            }

            $finalData[] = $data;
        }

        return $finalData;
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
}
