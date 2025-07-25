<?php

namespace App\Filament\Client\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int | string | array
    {
        return 12;
    }

    public static function getActiveNavigationIcon(): string|Htmlable|null
    {
        return str(self::getNavigationIcon())->replace('heroicon-o', 'heroicon-m')->toString();
    }
}
