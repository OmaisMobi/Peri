<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-6">
        {{ $this->form }}
        <x-filament::button type="submit">
            Save Settings
        </x-filament::button>
    </form>
</x-filament-panels::page>
