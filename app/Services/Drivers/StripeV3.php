<?php

namespace App\Services\Drivers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Services\Contracts\PaymentCurrency;
use App\Services\Contracts\PaymentGateway;
use Filament\Facades\Filament;

class StripeV3 extends Driver
{
    public static function process(Payment $payment): false|string
    {
        $stripeData = $payment->gateway->gateway_parameters;
        $alias = $payment->gateway->alias;
        \Stripe\Stripe::setApiKey($stripeData['secret_key']);

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'unit_amount' => round($payment->amount + $payment->charge, 2) * 100,
                        'currency' => "$payment->method_currency",
                        'product_data' => [
                            'name' => config('app.name', 'Default Product Name'),
                            'description' => 'Payment with Stripe',
                        ]
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'cancel_url' => route('payment.cancel', $payment->trx),
                'success_url' => route('payments.callback', ['gateway' => $alias]) . "?session={CHECKOUT_SESSION_ID}",
            ]);

        } catch (\Exception $e) {
            $send['error'] = true;
            $send['message'] = $e->getMessage();
            return json_encode($send);
        }

        $send['redirect'] = $session->url;
        $send['session'] = $session->id;
        $send['publishable_key'] = $stripeData['publishable_key'];
        $payment->method_code = json_decode(json_encode($session))->id;
        $payment->save();
        return json_encode($send);
    }

    public static function verify(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    {
        $StripeAcc = \App\Models\PaymentGateway::where('alias', 'StripeV3')->orderBy('id', 'desc')->firstOrFail();
        $gateway_parameter = $StripeAcc->gateway_parameters;

        \Stripe\Stripe::setApiKey($gateway_parameter['secret_key']);
        $stripeSession = $request->get('session');

        $session = \Stripe\Checkout\Session::retrieve($stripeSession);

        $payment = Payment::where('method_code',  $session->id)->where('status', 0)->firstOrFail();
        $payment_log = PaymentLog::where('payment_id', $payment->id)->firstOrFail();
        $payment_log->status = $session->status === 'complete' ? 1 : 2;
        $payment_log->response = $session;
        $payment_log->save();
        if ($session->status === 'complete') {
            self::paymentDataUpdate($payment);
            self::subscription($payment_log);
            return redirect($payment->success_url);
        }
        self::paymentDataUpdate($payment, true);
        return redirect($payment->failed_url);
    }

    public function integration(): array
    {
        return PaymentGateway::make('Stripe')
            ->alias('StripeV3')
            ->status(true)
            ->crypto(false)
            ->gateway_parameters([
                "secret_key" => "",
                "publishable_key" => ""
            ])
            ->supported_currencies([
                PaymentCurrency::make('USD')
                    ->symbol('$')
                    ->rate(1)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray()
            ])
            ->toArray();
    }

}
