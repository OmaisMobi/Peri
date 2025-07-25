<?php

namespace App\Providers\Filament;

use App\Facades\Helper;
use App\Filament\Widgets\Last30Earnings;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\SubscriberStatistics;
use App\Http\Middleware\ExtendedAuthenticate;
use App\Http\Middleware\VerifyIsAdmin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\App;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $settings = Helper::getGenralSettings();
        return $panel
            ->id('admin')
            ->path('admin')
            ->colors([
                'primary' => $settings['primary_color'] ?? '#193a66',
            ])
            ->favicon(asset('storage/' . $settings['favicon']))
            ->font('Poppins')
            ->brandLogo(asset('storage/' . $settings['main_logo']))
            ->darkModeBrandLogo(asset('storage/' . $settings['dark_logo']))
            ->brandLogoHeight('3rem')
            ->sidebarFullyCollapsibleOnDesktop()
            ->breadcrumbs(false)
            ->unsavedChangesAlerts()
            ->userMenuItems([
                MenuItem::make()
                    ->label('Dashboard')
                    ->icon('heroicon-o-computer-desktop')
                    ->url('/client')
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Subscriptions')
                    ->icon('heroicon-o-banknotes')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Payroll')
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Email Templates')
                    ->icon('heroicon-o-envelope')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Website Pages')
                    ->icon('heroicon-o-book-open')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                StatsOverview::class,
                Last30Earnings::class,
                SubscriberStatistics::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                VerifyIsAdmin::class
            ])
            ->authMiddleware([
                ExtendedAuthenticate::class,
            ])
            ->maxContentWidth(MaxWidth::Full)
            ->renderHook('panels::footer', fn() => view('filament.components.footer'))
            ->databaseNotificationsPolling('5s')
            ->databaseNotifications();
    }
}
