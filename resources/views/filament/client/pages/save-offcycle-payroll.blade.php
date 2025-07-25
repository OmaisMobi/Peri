<x-filament-panels::page>

    <div class="relative">
        {{-- Loading Overlay --}}
        <div wire:loading.delay.longest
            class="absolute inset-0 z-50 bg-white bg-opacity-80 flex items-center justify-center">
            <div class="flex flex-col items-center space-y-3">
                <svg class="w-8 h-8 text-primary-600" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="45" stroke="currentColor" stroke-width="4" fill="none"
                        class="opacity-20" />
                    <line x1="50" y1="50" x2="50" y2="20" stroke="currentColor"
                        stroke-width="3" stroke-linecap="round">
                        <animateTransform attributeName="transform" attributeType="XML" type="rotate" from="0 50 50"
                            to="360 50 50" dur="1s" repeatCount="indefinite" />
                    </line>
                    <circle cx="50" cy="50" r="2.5" fill="currentColor" />
                </svg>

                <div class="text-primary-600 text-sm font-medium">
                    Loading data...
                </div>
            </div>
        </div>
        {{-- Render the form defined in the Page class --}}
        <form wire:submit="save">
            {{ $this->form }}
            {{-- Add a button later to trigger the payroll generation --}}
            <div class="mt-6">
                <x-filament::button type="submit" icon="heroicon-c-check-circle">
                    Submit
                </x-filament::button>
            </div>
        </form>
    </div>
    {{-- Required for form actions like notifications --}}
    <x-filament-actions::modals />

</x-filament-panels::page>