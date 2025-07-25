<x-filament::page>
    <div class="space-y-6">
        {{ $this->form }}
        <div class="mt-4">
            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('attendancePolicies.manage'))
                <x-filament::button wire:click="submit">
                    Save Policies
                </x-filament::button>
            @endif
        </div>
    </div>
</x-filament::page>