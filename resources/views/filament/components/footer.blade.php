@php
    $settings = \App\Models\Setting::getByType('general');
@endphp

<div class="text-center text-gray-500 text-sm py-4">
    © {{ now()->year }} {{ $settings['footer_text'] }}
</div>
