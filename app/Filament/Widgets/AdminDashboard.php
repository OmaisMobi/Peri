<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AdminDashboard extends Widget
{
    protected static ?int $sort = 1;
    protected static string $view = 'filament.widgets.admin-dashboard';

    protected int|string|array $columnSpan = [
        'default' => 12,
        'md' => 12,
    ];

    public static function canView(): bool
    {
        return true;
    }
}
