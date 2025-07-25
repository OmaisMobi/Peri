<div>
    @if ($showComponent)
        <x-filament::button wire:click="recordAttendance" color="primary" size="lg" class="w-full">
            🕒 Record Attendance
        </x-filament::button>
    @endif
</div>