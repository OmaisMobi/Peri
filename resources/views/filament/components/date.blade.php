@php
    $settings = \App\Models\Setting::getByType('general');
    $primaryColor = $settings['primary_color'] ?? '#193a66';
@endphp

<div class="hidden lg:flex items-center space-x-2">
    <span
        class="px-3 py-1 rounded-lg text-sm font-medium shadow-md border bg-white text-gray-800 dark:bg-gray-800 dark:border-gray-600">
        <span id="current-date"></span>
    </span>
</div>

<script>
    // JavaScript to dynamically display the current date
    document.addEventListener('DOMContentLoaded', function() {
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        const today = new Date().toLocaleDateString('en-US', options);
        document.getElementById('current-date').innerText = today;
    });
</script>
