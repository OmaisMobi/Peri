<x-filament-panels::page>
    <div class="space-y-6">
        <!-- SMTP Configuration Form -->
        <form wire:submit.prevent="save" class="space-y-6">
            {{ $this->form }}
            
            <div class="flex items-center gap-3">
                <x-filament::button type="submit">
                    Save Settings
                </x-filament::button>
            </div>
        </form>
    </div>

    <!-- Make sure to include the action modal -->
    <x-filament-actions::modals />
</x-filament-panels::page>