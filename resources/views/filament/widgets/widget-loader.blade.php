<x-filament::widget>
    <div class="relative min-h-[300px]">
        <div wire:loading.flex class="absolute inset-0 bg-white/80 z-50 items-center justify-center">
            <x-filament::loading-indicator class="w-10 h-10 text-primary-600" />
        </div>
        <div class="relative overflow-hidden border rounded-xl shadow-sm">
            {{ $this->table }}
        </div>
    </div>
</x-filament::widget>
