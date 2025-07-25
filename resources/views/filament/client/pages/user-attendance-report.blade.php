<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Widgets Row -->
        <div class="flex flex-col lg:flex-row gap-6">
            <div class="flex-1 min-w-0">
                @livewire(\App\Filament\Client\CustomWidgets\UserAttendanceSummaryChart::class, ['userId' => request()->get('record')])
            </div>
            <div class="flex-1 min-w-0">
                @livewire(\App\Filament\Client\CustomWidgets\UserAMSreport::class, ['userId' => request()->get('record')])
            </div>
        </div>

        <!-- Leaves Section -->
        <div class="w-full">
            @include('filament.client.components.all-leaves', [
                'leaves' => \App\Models\Leave::where('user_id', request()->get('record'))->where(function ($query) {
                        $currentYearStart = \Carbon\Carbon::now()->startOfYear();
                        $currentYearEnd = \Carbon\Carbon::now()->endOfYear();
            
                        $query->whereBetween('starting_date', [$currentYearStart, $currentYearEnd])->orWhereBetween('ending_date', [$currentYearStart, $currentYearEnd])->orWhere(function ($query) use ($currentYearStart, $currentYearEnd) {
                                $query->where('starting_date', '<=', $currentYearStart)->where('ending_date', '>=', $currentYearEnd);
                            });
                    })->latest()->get(),
            ])
        </div>
    </div>
</x-filament-panels::page>
