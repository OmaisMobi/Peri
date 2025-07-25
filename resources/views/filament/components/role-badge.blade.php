@php
    $settings = \App\Models\Setting::getByType('general');
    $secondaryColor = $settings['secondary_color'] ?? '#193a66';
@endphp

@if ($role)
    <div class="hidden sm:flex items-center space-x-2">
        <span class="px-3 py-1 rounded-lg text-sm font-medium shadow-md border text-white dark:border-gray-600"
            style="background-color: {{ $secondaryColor }};">
            {{ $role === 'Admin' ? 'Admin' : ucfirst($role) }}
        </span>
    </div>
@endif
