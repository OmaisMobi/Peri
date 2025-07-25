<x-filament::widget>
    <div class="flex justify-between items-center p-4">
        {{-- Left-aligned heading --}}
        <h2 class="text-2xl font-semibold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
            <span class="block sm:hidden">Dashboard</span>
            <span class="hidden sm:block">Dashboard Overview</span>
        </h2>

        {{-- Right-aligned styled date picker --}}
        <div
            class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-1.5 shadow-sm transition focus-within:ring-2 focus-within:ring-primary-500">
            <x-filament::input type="date" wire:model.live="date" :max="now()->toDateString()"
                class="border-0 focus:ring-0 text-sm text-gray-700 dark:text-white bg-transparent px-0 py-0" />
        </div>
    </div>
</x-filament::widget>
