<x-filament-panels::page>
    {{-- Tabs UI --}}
    <div class="fi-tabs inline-flex justify-start gap-x-1 overflow-x-auto ml-0 rounded-xl bg-white p-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
        style="
    width: fit-content;">
        <button type="button" wire:click="setActiveTab('on_cycle')" @class([
            'fi-tabs-item group flex items-center justify-center gap-x-2 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75 fi-active fi-tabs-item-active bg-gray-50 dark:bg-white/5',
            'fi-tabs-item-active bg-white shadow dark:bg-white/5' =>
                $activeTab === 'on_cycle',
            'text-gray-500 dark:text-gray-400' => $activeTab !== 'on_cycle',
        ])>
            <x-heroicon-o-clock class="fi-tabs-item-icon h-5 w-5" />
            Ongoing
        </button>

        <button type="button" wire:click="setActiveTab('off_cycle')" @class([
            'fi-tabs-item group flex items-center justify-center gap-x-2 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75 fi-active fi-tabs-item-active bg-gray-50 dark:bg-white/5',
            'fi-tabs-item-active bg-white shadow dark:bg-white/5' =>
                $activeTab === 'off_cycle',
            'text-gray-500 dark:text-gray-400' => $activeTab !== 'off_cycle',
        ])>
            <x-heroicon-c-calendar-date-range class="fi-tabs-item-icon h-5 w-5" />
            History
        </button>
    </div>

    {{-- Content Area: Dynamically render based on active tab --}}
    @if ($activeTab === 'on_cycle')
        {{ $this->table }}
        @livewire('off-cycle-payroll-table')
    @elseif ($activeTab === 'off_cycle')
        @livewire(\App\Filament\Client\PayRunWidgets\PayRunHistoryTableWidget::class)
    @endif
</x-filament-panels::page>