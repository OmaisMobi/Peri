<x-filament-panels::page>
    <div class="flex gap-6">
        <div class="w-1/2">
            <form wire:submit="save">
                {{ $this->form }}
                <div class="mt-6">
                    <x-filament::button type="submit" icon="heroicon-c-check-circle">
                        Save
                    </x-filament::button>
                </div>
            </form>
        </div>

        <div class="w-1/2">
            {{ $this->table }}
        </div>
    </div>
    <x-filament-actions::modals />
</x-filament-panels::page>
