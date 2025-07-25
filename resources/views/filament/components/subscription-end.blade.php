@if ($role === 'Admin')
    <div class="hidden sm:flex items-center space-x-2">
        @if ($daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 7)
            <span class="px-3 py-1 rounded-lg text-sm font-medium shadow-md border text-white dark:border-gray-600"
                style="background-color: #ef4444;">
                Subscription Ends In {{ $daysLeft }} Day{{ $daysLeft !== 1 ? 's' : '' }}
            </span>
        @endif
    </div>
@endif
