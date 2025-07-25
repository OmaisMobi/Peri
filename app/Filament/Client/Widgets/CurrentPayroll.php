<?php

namespace App\Filament\Client\Widgets;

use Filament\Widgets\Widget;
use Filament\Facades\Filament;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CurrentPayroll extends Widget
{
    protected static string $view = 'filament.widgets.current-payroll-card';
    protected static ?int $sort = 6;

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 12,
        ];
    }

    public function getViewData(): array
    {
        $latestPayRun = Filament::getTenant()->payRuns()->latest()->first();

        $totalNetPay = 0;
        $status = 'Not Run';
        $employeeCount = 0;
        $totalTax = 0;
        $payRunMonthYear = null;
        $currencySymbol = $this->getCurrencySymbol();

        if ($latestPayRun) {
            $payrolls = $latestPayRun->payrolls;
            $totalNetPay = $payrolls->sum('net_payable_salary');
            $status = $latestPayRun->paid ? 'Paid' : strtoupper($latestPayRun->status);
            $employeeCount = $payrolls->count();
            foreach ($payrolls as $payroll) {
                if (isset($payroll->tax_data['monthly_tax_calculated'])) {
                    $totalTax += $payroll->tax_data['monthly_tax_calculated'];
                }
            }
            $payRunMonthYear = Carbon::parse($latestPayRun->pay_period_start_date)->format('F Y');
        }

        $totalActiveEmployees = Filament::getTenant()->users()->where('active', 1)->count();

        return [
            'totalNetPay' => $totalNetPay,
            'status' => $status,
            'employeeCount' => $employeeCount,
            'totalTax' => $totalTax,
            'totalEmployees' => $totalActiveEmployees,
            'payRunMonthYear' => $payRunMonthYear,
            'currencySymbol' => $currencySymbol,
        ];
    }

    protected function getCurrencySymbol(): string
    {
        $country = Filament::getTenant()->country_id;

        return $country
            ? DB::table('tax_slabs')->where('country_id', $country)->value('salary_currency') ?? ''
            : '';
    }

    protected function formatCurrency(string $symbol, float|int $amount): string
    {
        return $symbol . ' ' . number_format(round($amount));
    }
}
