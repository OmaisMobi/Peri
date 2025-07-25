<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Last30Earnings extends ChartWidget
{
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '355px';

    protected int|string|array $columnSpan = [
        'default' => 12,
        'md' => 8,
    ];

    private ?array $chartData = null;

    public static function canView(): bool
    {
        return true;
    }

    protected function getChartData(): array
    {
        if ($this->chartData) {
            return $this->chartData;
        }

        $dates = collect();
        $earnings = collect();
        $total = 0;

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $formattedDate = $date->format('M d');
            $dates->push($formattedDate);

            $dailyTotal = Payment::whereDate('created_at', $date)
                ->where('status', '1')
                ->sum('final_amount');

            $earnings->push($dailyTotal);
            $total += $dailyTotal;
        }

        return $this->chartData = [
            'datasets' => [
                [
                    'label' => 'Earning $',
                    'data' => $earnings->toArray(),
                ],
            ],
            'labels' => $dates->toArray(),
            'total' => $total,
        ];
    }

    protected function getData(): array
    {
        $data = $this->getChartData();

        return [
            'datasets' => $data['datasets'],
            'labels' => $data['labels'],
        ];
    }

    public function getHeading(): string
    {
        $data = $this->getChartData();

        return 'Last 30 Days Payments ($' . number_format($data['total'], 2) . ')';
    }

    protected function getType(): string
    {
        return 'line';
    }
}
