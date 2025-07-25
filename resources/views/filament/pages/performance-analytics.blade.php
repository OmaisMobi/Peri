<x-filament-panels::page>
    <div class="grid grid-cols-2 gap-6 xl:grid-cols-2 sm:grid-cols-1">
        @livewire(\Dotswan\FilamentLaravelPulse\Widgets\PulseCache::class)
        @livewire(\Dotswan\FilamentLaravelPulse\Widgets\PulseExceptions::class)
        @livewire(\Dotswan\FilamentLaravelPulse\Widgets\PulseSlowRequests::class)
        @livewire(\Dotswan\FilamentLaravelPulse\Widgets\PulseUsage::class)
        @livewire(\Dotswan\FilamentLaravelPulse\Widgets\PulseSlowQueries::class)
    </div>
</x-filament-panels::page>
