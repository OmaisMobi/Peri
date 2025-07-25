<div class="w-full">
    <div
        class="fi-ta-ctn divide-y divide-gray-200 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-ta-header-ctn divide-y divide-gray-200 dark:divide-white/10">
            <div class="fi-ta-header-toolbar flex items-center justify-between gap-x-4 px-6 py-4 sm:px-6">
                <div class="flex shrink-0 items-center gap-x-4">
                    <h3 class="fi-ta-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        Leave Records
                    </h3>
                </div>
            </div>
        </div>

        <div class="fi-ta-content relative divide-y divide-gray-200 dark:divide-white/10 dark:border-t-white/10 overflow-x-auto">
            <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                <thead class="fi-ta-header-ctn divide-y divide-gray-200 dark:divide-white/10">
                    <tr class="bg-gray-50 dark:bg-white/5">
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                            <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                <span
                                    class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                    Type
                                </span>
                            </span>
                        </th>
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                            <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                <span
                                    class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                    Category
                                </span>
                            </span>
                        </th>
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                            <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                <span
                                    class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                    Starting Time
                                </span>
                            </span>
                        </th>
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                            <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                <span
                                    class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                    Ending Time
                                </span>
                            </span>
                        </th>
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                            <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                <span
                                    class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                    Duration
                                </span>
                            </span>
                        </th>
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                            <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                <span
                                    class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                    Reason
                                </span>
                            </span>
                        </th>
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                            <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                <span
                                    class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                    Status
                                </span>
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody class="fi-ta-body divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                    @forelse($leaves as $leave)
                        <tr
                            class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                            <td
                                class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                <div class="fi-ta-col-wrp">
                                    <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                        <div class="flex">
                                            <div
                                                class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white">
                                                {{ ucwords(str_replace('_', ' ', $leave->type)) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td
                                class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                <div class="fi-ta-col-wrp">
                                    <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                        <div class="flex">
                                            <div
                                                class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white">
                                                {{ $leave->leave_type }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td
                                class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                <div class="fi-ta-col-wrp">
                                    <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                        <div class="flex">
                                            <div
                                                class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white">
                                                @php
                                                    $date = $leave->starting_date
                                                        ? \Carbon\Carbon::parse($leave->starting_date)->format('Y-m-d')
                                                        : null;

                                                    $time = $leave->starting_time
                                                        ? \Carbon\Carbon::parse($leave->starting_time)->format('H:i:s')
                                                        : null;

                                                    if ($date && $time) {
                                                        echo "$date $time";
                                                    } elseif ($date) {
                                                        echo $date;
                                                    } else {
                                                        echo '-';
                                                    }
                                                @endphp
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td
                                class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                <div class="fi-ta-col-wrp">
                                    <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                        <div class="flex">
                                            <div
                                                class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white">
                                                @php
                                                    $date = $leave->ending_date
                                                        ? \Carbon\Carbon::parse($leave->ending_date)->format('Y-m-d')
                                                        : null;

                                                    $time = $leave->ending_time
                                                        ? \Carbon\Carbon::parse($leave->ending_time)->format('H:i:s')
                                                        : null;

                                                    if ($date && $time) {
                                                        echo "$date $time";
                                                    } elseif ($date) {
                                                        echo $date;
                                                    } else {
                                                        echo '-';
                                                    }
                                                @endphp
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td
                                class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                <div class="fi-ta-col-wrp">
                                    <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                        <div class="flex">
                                            <div
                                                class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white">
                                                @php
                                                    if ($leave->type === 'regular') {
                                                        $start = \Carbon\Carbon::parse($leave->starting_date);
                                                        $end = \Carbon\Carbon::parse($leave->ending_date);
                                                        $days = $start->diffInDays($end) + 1;
                                                        $duration = "{$days} Full Day" . ($days > 1 ? 's' : '');
                                                    } elseif ($leave->type === 'half_day') {
                                                        $duration = ucfirst($leave->half_day_timing) . ' Half Day';
                                                    } elseif ($leave->type === 'short_leave') {
                                                        $startTime = \Carbon\Carbon::parse($leave->starting_time);
                                                        $endTime = \Carbon\Carbon::parse($leave->ending_time);
                                                        $minutes = round($startTime->diffInMinutes($endTime));
                                                        $duration = "{$minutes} minutes";
                                                    } else {
                                                        $duration = 'N/A';
                                                    }
                                                @endphp
                                                {{ $duration }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td
                                class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                <div class="fi-ta-col-wrp">
                                    <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                        <div class="flex">
                                            <div
                                                class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white">
                                                {{ Str::limit($leave->leave_reason, 90) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td
                                class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                <div class="fi-ta-col-wrp">
                                    <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                        <div class="flex">
                                            <div
                                                class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white">
                                                <x-filament::badge
                                                    color="{{ match ($leave->status) {
                                                        'approved' => 'success',
                                                        'rejected' => 'danger',
                                                        'pending' => 'warning',
                                                        default => 'gray',
                                                    } }}">
                                                    {{ ucfirst($leave->status) }}
                                                </x-filament::badge>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="fi-ta-empty-state-row">
                            <td colspan="7" class="fi-ta-empty-state-cell p-6">
                                <div
                                    class="fi-ta-empty-state-content mx-auto grid max-w-lg justify-items-center text-center">
                                    <div
                                        class="fi-ta-empty-state-icon-ctn mb-4 rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                                        <svg class="fi-ta-empty-state-icon h-6 w-6 text-gray-500 dark:text-gray-400"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <h4
                                        class="fi-ta-empty-state-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                        No leave records found
                                    </h4>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
