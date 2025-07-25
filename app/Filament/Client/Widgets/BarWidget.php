<?php

namespace App\Filament\Client\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedChartWidget;

class BarWidget extends AdvancedChartWidget
{
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = [
        'default' => 12,
        'md' => 8,
    ];
    protected static ?string $maxHeight = '440px';
    protected static ?string $heading = 'Attendance Analytics';

    public ?string $filter = 'year';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'month' => 'This month',
            'year' => 'This year',
        ];
    }

    protected function getData(): array
    {
        return [
            'labels' => ['Januaray', 'February', 'March', 'April'],
            'datasets' => [
                [
                    'label' => 'Present',
                    'data' => [30, 40, 35, 20],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.7)',
                ],
                [
                    'label' => 'Absent',
                    'data' => [5, 7, 3, 4],
                    'backgroundColor' => 'rgba(255, 99, 132, 0.7)',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected static ?array $options = [
        'scales' => [
            'x' => [
                'stacked' => true,
            ],
            'y' => [
                'stacked' => true,
            ],
        ],
    ];
}
