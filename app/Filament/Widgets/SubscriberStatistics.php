<?php

namespace App\Filament\Widgets;

use App\Models\Plan;
use App\Models\Subscription;
use ArberMustafa\FilamentGoogleCharts\Widgets\DonutChartWidget;

use Illuminate\Support\Facades\Auth;

class SubscriberStatistics extends DonutChartWidget
{
    protected static ?string $heading = 'Subscribed Plans';
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '355px';

    protected int|string|array $columnSpan = [
        'default' => 12,
        'md' => 4,
    ];

    public static function canView(): bool
    {
        return true;
    }

    protected static ?array $options = [
        'legend' => [
            'position' => 'top',
        ],
        'height' => 355,
    ];

    protected function getData(): array
    {
        $plans = Plan::all();
        $chartData = [['Plan', 'Subscribers']];
        foreach ($plans as $plan) {
            $count = count(Subscription::byPlanId($plan->id)->get());
            $chartData[] = [$plan->name, $count];
        }
        return $chartData;
    }
}
