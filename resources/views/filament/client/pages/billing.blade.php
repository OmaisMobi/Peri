{{-- resources/views/billing.blade.php --}}
<div class="billing-container">
    {{-- Left Sidebar --}}
    <div class="billing-sidebar">
        <x-filament::link href="{{ url(filament()->getCurrentPanel()->getId()) }}" class="mt-2 logo inline-block">
            <div class="logo">
                <span class="flex items-center gap-2 text-white">
                    <x-heroicon-c-arrow-uturn-left class="w-5 h-5 text-white" />
                    <span>Return to Dashboard</span>
                </span>
            </div>
        </x-filament::link>
    </div>

    {{-- Main Content Area --}}
    <div class="billing-main">

        <div class="px-4 my-8 flex flex-col gap-6">
            @php
                $currentPlan = $tenant->subscribedPlans()->first();
            @endphp

            @if (!$currentPlan)
                <div class="mb-6">
                    <div
                        class="p-4 bg-blue-100 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg shadow-sm">
                        <div class="max-w-2xl text-sm text-blue-800 dark:text-blue-200 flex items-center gap-2">
                            <x-filament::icon-button icon="heroicon-m-information-circle" label="Info"
                                class="flex-shrink-0" />
                            It looks like you have no active subscription.
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse ($plans as $plan)
                    <x-filament::section
                        class="billing-plan-section {{ $currentPlan && $currentPlan->id === $plan->id ? 'ring-2 ring-primary-500' : '' }}">
                        <x-slot name="heading">
                            <h4 class="text-xl font-semibold">{{ $plan->name }}</h4>
                        </x-slot>
                        <x-slot name="description">
                            {{ $plan->description }}
                        </x-slot>

                        <div class="mb-4">
                            @if ($plan->isFree())
                                <span class="text-4xl font-bold text-gray-900 dark:text-white">Free</span>
                            @else
                                <span class="text-4xl font-bold text-gray-900 dark:text-white">
                                    ${{ number_format($plan->price + $plan->signup_fee, 2) }}
                                </span>
                                <span class="text-gray-500 dark:text-gray-400">
                                    /
                                    {{ $plan->invoice_period > 1 ? $plan->invoice_period : '' }}
                                    {{ $plan->invoice_interval }}
                                </span>
                                @if ($plan->hasTrial())
                                    <br>
                                    <span class="text-sm text-gray-400">
                                        {{ $plan->trial_period }} {{ $plan->trial_interval }} trial
                                    </span>
                                @endif
                            @endif
                        </div>

                        <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            @foreach ($plan->features as $feature)
                                <li class="flex items-center gap-2">
                                    @if (is_numeric($feature->value) || $feature->value > 0)
                                        <x-filament::icon-button icon="heroicon-m-check-circle" label="Included"
                                            class="text-green-500 flex-shrink-0" />
                                    @else
                                        <x-filament::icon-button icon="heroicon-m-x-circle" label="Not Included"
                                            class="text-gray-400 flex-shrink-0" />
                                    @endif
                                    <span>
                                        {{ $feature->name }}
                                        @if (is_numeric($feature->value) || $feature->value === 'unlimited')
                                            ({{ ucfirst($feature->value) }})
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-6">
                            <x-filament::button
                                color="{{ $this->tenant->planSubscriptions()->first()?->plan()?->is($plan) && $this->tenant->planSubscriptions()->first()?->active() ? 'success' : 'primary' }}"
                                icon="{{ $this->tenant->planSubscriptions()->first()?->plan()?->is($plan) && $this->tenant->planSubscriptions()->first()?->active() ? 'heroicon-s-check-circle' : 'heroicon-s-arrows-right-left' }}"
                                wire:click="subscribe({{ $plan->id }})">
                                {{ $this->textByPlan($plan) }}
                            </x-filament::button>
                        </div>
                    </x-filament::section>
                @empty
                    <div class="col-span-full">
                        <x-filament::card class="text-center py-8">
                            <p class="text-gray-600 dark:text-gray-400">No plans available at this time.</p>
                        </x-filament::card>
                    </div>
                @endforelse
            </div>

            @if ($currentSubscription && $currentSubscription->active())
                <x-filament::section class="billing-plan-section mb-4">
                    <x-slot name="heading">
                        <h3 class="text-xl font-bold">Cancel Subscription</h3>
                    </x-slot>
                    <div class="max-w-full text-sm text-justify text-gray-600 dark:text-gray-400 space-y-3">
                        <p>You can cancel your active subscription below. If you cancel your subscription, you'll
                            continue to have access to all paid features until the end of your current billing
                            period.</p>
                        <div class="warning-notice max-w-fit">
                            <x-heroicon-o-exclamation-triangle class="warning-icon" />
                            <p class="warning-text">
                                This action cannot be undone. However you can always resubscribe later.
                            </p>
                        </div>
                    </div>
                    <div class="mt-6">
                        {{ $this->cancelPlanAction }}
                    </div>
                </x-filament::section>
            @endif
        </div>
    </div>
</div>
