<x-filament::widget>
    @if ($this->getViewData()['payRunMonthYear'])
        {{-- Header Section --}}
        <div class="border-b border-gray-200 dark:border-gray-700 p-4 pt-0">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex flex-col sm:flex-row sm:items-baseline gap-2 sm:gap-4">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                        Pay Run for {{ $this->getViewData()['payRunMonthYear'] }}
                    </h2>
                    {{-- Status Badge inline with heading --}}
                    @php
                        $status = $this->getViewData()['status'];
                        $statusColor = match (strtolower($status)) {
                            'finalized' => 'bg-info-200 text-info-600',
                            'paid' => 'bg-success-200 text-success-600',
                            'pending_approval' => 'bg-warning-200 text-warning-600',
                            'draft' => 'bg-warning-200 text-warning-600',
                            'rejected' => 'bg-danger-200 text-danger-600',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
                        };
                        $formattedStatus = match (strtolower($status)) {
                            'finalized' => 'Approved',
                            default => ucwords(str_replace('_', ' ', strtolower($status))),
                        };
                    @endphp
                    <span
                        class="inline-flex text-center items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }} self-start">
                        <span class="w-2 h-2 rounded-full"></span>
                        {{ $formattedStatus }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 sm:mt-0">
                    Overview of current payroll processing
                </p>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="pt-4 pb-4 grid grid-cols-1 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-4 gap-6">
            {{-- Total Net Pay - Primary Metric --}}
            <div class="lg:col-span-1 xl:col-span-4">
                <div
                    class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 
                           rounded-xl p-6 border border-primary-200 dark:bg-gray-900 dark:border-primary-800 
                           hover:shadow-lg transition-all duration-200 group">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="p-2 bg-primary-500 rounded-lg group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                    d="M8 7V6a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1M3 18v-7a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">
                        Total Net Pay
                    </h3>
                    <p class="text-2xl font-semibold text-primary-900 dark:text-white">
                        {{ $this->formatCurrency($this->getViewData()['currencySymbol'], $this->getViewData()['totalNetPay']) }}
                    </p>
                </div>
            </div>

            {{-- Total Tax --}}
            <div
                class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 
                           rounded-xl p-6 border border-primary-200 dark:bg-gray-900 dark:border-primary-800 
                           hover:shadow-lg transition-all duration-200 group">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-primary-500 rounded-lg group-hover:scale-110 transition-transform duration-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">
                    Total Tax
                </h3>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $this->formatCurrency($this->getViewData()['currencySymbol'], $this->getViewData()['totalTax']) }}
                </p>
            </div>

            {{-- Employees Processed --}}
            <div
                class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 
                           rounded-xl p-6 border border-primary-200 dark:bg-gray-900 dark:border-primary-800 
                           hover:shadow-lg transition-all duration-200 group">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-primary-500 rounded-lg group-hover:scale-110 transition-transform duration-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">
                    Employees Processed
                </h3>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ number_format($this->getViewData()['employeeCount']) }}
                </p>
            </div>

            {{-- Skipped Employees --}}
            <div
                class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 
                           rounded-xl p-6 border border-primary-200 dark:bg-gray-900 dark:border-primary-800 
                           hover:shadow-lg transition-all duration-200 group">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-primary-500 rounded-lg group-hover:scale-110 transition-transform duration-200">
                        <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 4h12M6 4v16M6 4H5m13 0v16m0-16h1m-1 16H6m12 0h1M6 20H5M9 7h1v1H9V7Zm5 0h1v1h-1V7Zm-5 4h1v1H9v-1Zm5 0h1v1h-1v-1Zm-3 4h2a1 1 0 0 1 1 1v4h-4v-4a1 1 0 0 1 1-1Z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">
                    Total Active Employees
                </h3>
                <p
                    class="text-2xl font-semibold {{ $this->getViewData()['totalEmployees'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                    {{ number_format($this->getViewData()['totalEmployees']) }}
                </p>
            </div>
        </div>
    @else
        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
            No Pay Run yet
        </div>
    @endif
</x-filament::widget>
