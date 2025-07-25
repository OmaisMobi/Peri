<div>
    <style>
        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            color: #333;
        }

        .payslip-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            background-color: white;
            color: #333;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }

        .details-grid p {
            margin: 5px 0;
            font-size: 14px;
        }

        .section-title {
            font-size: 20px;
            margin-bottom: 15px;
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
            color: #0056b3;
        }

        .payslip-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: #fff;
            table-layout: fixed;
        }

        .payslip-container table th,
        .payslip-container table td {
            border: 1px solid #ddd;
            padding: 10px 12px;
            text-align: left;
            font-size: 14px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .payslip-container table thead th {
            background-color: #e9e9e9;
            font-weight: bold;
            color: #333;
        }

        .payslip-container table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .totals-table td {
            font-size: 16px;
            font-weight: normal;
            text-align: left;
        }

        .totals-table tbody tr:first-child td {
            font-weight: bold;
        }

        .totals-table th {
            font-size: 16px;
            font-weight: bold;
        }
    </style>

    @php
        use App\Models\CompanyDetail;
        use App\Models\Setting;
        use App\Models\TaxSlabs;
        use App\Models\Team;

        $settings = Setting::getByType('general');

        function numberToWords($number, $symbol)
        {
            $currencyNames = [
                '$' => 'Dollars',
                'Rs' => 'Rupees',
                '₹' => 'Rupees',
                '€' => 'Euros',
                '£' => 'Pounds',
            ];
            $cleanSymbol = trim($symbol);
            $currencyName = $currencyNames[$cleanSymbol] ?? 'Currency';
            $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
            return $currencyName . ' ' . ucfirst($formatter->format((int) round($number))) . ' only';
        }

        $contextAdminId = auth()->user()->latest_team_id;
        $company = Team::where('id', $contextAdminId)->first();
        $companyName = $company?->name ?? 'Company';

        $currencySymbols = [
            'USD' => '$ ',
            'PKR' => 'Rs ',
            'INR' => '₹ ',
            'EUR' => '€ ',
            'GBP' => '£ ',
            'AED' => 'د.إ ',
        ];

        $currencyCode = optional(TaxSlabs::where('country_id', $company?->country_id)->first())->salary_currency;
        $symbol = $currencySymbols[$currencyCode] ?? '¤';

        $earningsData = $payroll->earnings ?? [];
        $deductionsData = $payroll->deductions ?? [];

        $baseSalary = $payroll->base_salary;
        $totalEarnings = $baseSalary + collect($earningsData)->sum('amount');
        $totalDeductions = $payroll->tax + collect($deductionsData)->sum('amount');
    @endphp

    <div class="payslip-container">
        <div class="details-grid">
            <div>
                <p><strong>Name:</strong> {{ $payroll->user->name }}</p>
            </div>
            <div>
                <p><strong>Period:</strong> {{ $payroll->period_start->format('j M Y') }} -
                    {{ $payroll->period_end->format('j M Y') }}</p>
            </div>
        </div>

        <div class="payroll-components">
            <div class="section-title">Earnings</div>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Base Salary</td>
                        <td>{{ $symbol }}{{ number_format($baseSalary) }}</td>
                    </tr>
                    @foreach ($earningsData as $earning)
                        <tr>
                            <td>{{ $earning['title'] }}</td>
                            <td>{{ $symbol }}{{ number_format($earning['amount']) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td><strong>Total Earnings</strong></td>
                        <td><strong>{{ $symbol }}{{ number_format($totalEarnings) }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <div class="section-title">Deductions</div>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($deductionsData as $deduction)
                        <tr>
                            <td>{{ $deduction['title'] }}</td>
                            <td>{{ $symbol }}{{ number_format($deduction['amount']) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>Tax</td>
                        <td>{{ $symbol }}{{ number_format($payroll->tax) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Deductions</strong></td>
                        <td><strong>{{ $symbol }}{{ number_format($totalDeductions) }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <table class="totals-table">
                <thead>
                    <tr>
                        <th>Net Pay</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $symbol }}{{ number_format($payroll->net_pay) }}</td>
                    </tr>
                    <tr>
                        <td>{{ numberToWords($payroll->net_pay, $symbol) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>