<?php

namespace App\Providers;

use App\Events\ChangePlan;
use App\Events\RenewPlan;
use App\Events\SubscribePlan;
use Illuminate\Support\ServiceProvider;
use App\Facades\FilamentPayments;
use App\Services\Contracts\PaymentBillingInfo;
use App\Services\Contracts\PaymentCustomer;
use App\Services\Contracts\PaymentRequest;
use App\Services\Contracts\PaymentShippingInfo;
use App\Facades\FilamentSubscriptions;
use App\Filament\Client\Pages\PaymentSuccess;
use App\Models\Payment;
use App\Services\Contracts\Payload;
use Illuminate\Http\RedirectResponse;
use App\Models\Plan;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class FilamentPaymentsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('filament-payments', function () {
            return new \App\Services\FilamentPaymentsServices();
        });
    }

    /**
     * Bootstrap services.
     */


    public function boot(): void
    {
        FilamentSubscriptions::beforeSubscription(function ($data) {
            $this->PaymentPage($data, SubscribePlan::class);
        });

        FilamentSubscriptions::beforeRenew(function ($data) {
            $this->PaymentPage($data, RenewPlan::class);
        });
        FilamentSubscriptions::beforeChange(function ($data) {
            $this->PaymentPage($data, ChangePlan::class);
        });
        FilamentSubscriptions::afterSubscription(function ($data) {
            $team_name = Team::find($data['team_id'])?->name ?? 'Unknown Team';
            User::where('is_super_admin', true)->get()->each(function ($user) use ($team_name) {
                Notification::make()
                    ->title('New Subscription')
                    ->success()
                    ->body("New subscription for " . $team_name . " has been successfully created.")
                    ->sendToDatabase($user, isEventDispatched: true);
            });
            Notification::make()
                ->title('Subscription Completed')
                ->body("Your subscription has been successfully created.")
                ->success();
        });
        FilamentSubscriptions::afterRenew(function ($data) {
            $team_name = Team::find($data['team_id'])?->name ?? 'Unknown Team';
            User::where('is_super_admin', true)->get()->each(function ($user) use ($team_name) {
                Notification::make()
                    ->title('Subscription Renew')
                    ->success()
                    ->body("Subscription renewal for " . $team_name . ".")
                    ->sendToDatabase($user, isEventDispatched: true);
            });
            Notification::make()
                ->title('Subscription Renew')
                ->body("Your subscription has been successfully renewed.")
                ->success();
        });
        FilamentSubscriptions::afterCanceling(function ($data) {
            $team_name = Team::find($data['team_id'])?->name ?? 'Unknown Team';
            User::where('is_super_admin', true)->get()->each(function ($user) use ($team_name) {
                Notification::make()
                    ->title('Subscription Cancelled')
                    ->success()
                    ->body("Subscription cancelled by " . $team_name . ".")
                    ->sendToDatabase($user, isEventDispatched: true);
            });
            Notification::make()
                ->title('Subscription Cancelled')
                ->body("Your subscription has been cancelled.")
                ->success();
        });

        FilamentSubscriptions::afterChange(function ($data) {
            $team_name = Team::find($data['team_id'])?->name ?? 'Unknown Team';
            User::where('is_super_admin', true)->get()->each(function ($user) use ($team_name) {
                Notification::make()
                    ->title('Subscription Changed')
                    ->success()
                    ->body("Subscription changed by " . $team_name . ".")
                    ->sendToDatabase($user, isEventDispatched: true);
            });
            Notification::make()
                ->title('Subscription Changed')
                ->body("Your subscription has been changed.")
                ->success();
        });
    }
    private function PaymentPage($data, $event)
    {
        return redirect()->to(
            FilamentPayments::pay(
                data: PaymentRequest::make(Plan::class)
                    ->model_id($data['new']->id)
                    ->team_id($data['team_id'])
                    ->event($event)
                    ->currency('USD')
                    ->amount($data['new']->price)
                    ->details('Subscription Payment')
                    ->success_url(url('/client'))
                    ->cancel_url(url('/client'))
                    ->customer(
                        PaymentCustomer::make('John Doe')
                            ->email('john@gmail.com')
                            ->mobile('+201207860084')
                    )
                    ->billing_info(
                        PaymentBillingInfo::make('123 Main St')
                            ->area('Downtown')
                            ->city('Cairo')
                            ->state('Cairo')
                            ->postcode('12345')
                            ->country('EG')
                    )
                    ->shipping_info(
                        PaymentShippingInfo::make('123 Main St')
                            ->area('Downtown')
                            ->city('Cairo')
                            ->state('Cairo')
                            ->postcode('12345')
                            ->country('EG')
                    )
            )
        );
    }
}
