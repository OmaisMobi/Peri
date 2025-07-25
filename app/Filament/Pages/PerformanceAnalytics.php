<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Dotswan\FilamentLaravelPulse\Widgets\PulseCache;
use Dotswan\FilamentLaravelPulse\Widgets\PulseExceptions;
use Dotswan\FilamentLaravelPulse\Widgets\PulseQueues;
use Dotswan\FilamentLaravelPulse\Widgets\PulseSlowQueries;
use Dotswan\FilamentLaravelPulse\Widgets\PulseSlowRequests;
use Dotswan\FilamentLaravelPulse\Widgets\PulseUsage;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Illuminate\Contracts\Support\Htmlable;

class PerformanceAnalytics extends page
{
    use HasFiltersAction;
    protected static string $view = 'filament.pages.performance-analytics';

    protected static ?string $navigationLabel = 'Performance Analytics';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?int    $navigationSort = 99999;

    public static function getActiveNavigationIcon(): string|Htmlable|null
    {
        return str(self::getNavigationIcon())->replace('heroicon-o', 'heroicon-s')->toString();
    }

    public function getWidgets(): array
    {
        return [
            PulseCache::class,
            PulseExceptions::class,
            PulseUsage::class,
            PulseQueues::class,
            PulseSlowQueries::class,
            PulseSlowRequests::class,
        ];
    }
}
