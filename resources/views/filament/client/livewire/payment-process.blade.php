<div class="bg-gray-100 dark:bg-gray-900 min-h-screen">
    <div class="container mx-auto px-4 py-8 md:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Payment Details --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 md:p-8">
                <div class="flex items-center justify-between mb-6">
                    <x-filament-panels::logo class="h-10 w-auto text-primary-500" />

                    @if (auth()->check())
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <p>Signed in as <span class="font-semibold">{{ auth()->user()->name }}</span>.</p>
                            <p>Not you? <a href="#" class="text-primary-500 hover:underline">Sign out</a></p>
                        </div>
                    @endif
                </div>

                <h2 class="text-xl md:text-2xl mt-4 mb-4 font-semibold text-gray-700 dark:text-gray-200">Payment
                    Details</h2>

                <div class="space-y-4 text-gray-600 dark:text-gray-300">
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Payment For:</span>
                        <span class="font-semibold text-gray-800 dark:text-white">{{ $payment->detail }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Subscription Plan:</span>
                        <span class="font-semibold text-gray-800 dark:text-white">{{ $payment->model->name }}</span>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 my-6"></div>

                    <div class="flex justify-between items-center">
                        <span>Amount</span>
                        <span>{{ Number::currency($payment->amount, $payment->method_currency) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span>Gateway Fee</span>
                        <span>{{ Number::currency($payment->charge, $payment->method_currency) }}</span>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 my-6"></div>

                    <div class="flex justify-between items-center text-xl font-bold">
                        <span class="text-gray-800 dark:text-white">Total Payable</span>
                        <span
                            class="text-primary-600 dark:text-primary-400">{{ Number::currency($payment->final_amount, $payment->method_currency) }}</span>
                    </div>
                </div>
            </div>

            {{-- Payment Method --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 md:p-8">
                <div class="space-y-4">
                    {{ $this->form }}
                </div>

                <div class="mt-4 flex items-center justify-start gap-4">
                    {{ $this->paymentAction }}
                    <x-filament::link href="{{ url(filament()->getCurrentPanel()->getId()) }}" color="gray"
                        class="inline-block">
                        <div
                            class="logo-back max-w-fit fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-success fi-color-success fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 fi-ac-action fi-ac-btn-action">

                            <span class="flex items-center text-white">
                                <span>Cancel</span>
                            </span>

                        </div>
                    </x-filament::link>
                </div>

                <div class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    <p>&copy; {{ date('Y') }} {{ env('APP_NAME', 'Laravel') }}. All rights reserved.</p>
                    <p class="mt-1">Secure payments powered by trusted gateways.</p>
                </div>
            </div>

        </div>
    </div>

    <x-filament-actions::modals />
</div>
