<?php

namespace App\Livewire;

use App\Filament\Client\Pages\Billing;
use Filament\Facades\Filament;
use Livewire\Component;

class SubscriptionCard extends Component
{
    public string $redirectUrl;
    public string $text;

    public function mount()
    {
        $team = Filament::getTenant();
        $subscription = $team->activePlanSubscriptions()->first();
        if (
            $subscription &&
            $subscription->trial_ends_at &&
            $subscription->trial_ends_at->isBetween(now(), now()->addDays(7))
        ) {
            $this->text = 'Free trial ending soon';
        }

        $this->redirectUrl = Billing::getUrl();
    }
    public function render()
    {
        return view('livewire.subscription-card');
    }
}
