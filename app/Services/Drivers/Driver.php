<?php

namespace App\Services\Drivers;

use App\Facades\FilamentSubscriptions;
use App\Models\Account;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use App\Models\Payment;
use Carbon\Carbon;
use App\Models\Plan;
use Illuminate\Support\Facades\Event;

abstract class Driver
{
    public static abstract function process(Payment $payment): false|string;
    public static abstract function verify(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector;
    public abstract function integration(): array;

    public static function cancel($trx)
    {
        $payment = Payment::where('trx', $trx)->where('status', 0)->firstOrFail();

        // Update Status
        $payment->status = 2;
        $payment->save();

        return redirect($payment->failed_url);
    }

    public static function initiate(Request $request)
    {
        $rules = [
            'public_key' => 'required|string|max:50',
            'currency' => 'required|string|size:3|uppercase|in:USD',
            'amount' => 'required|numeric|min:1',
            'details' => 'required|string|max:100',
            'success_url' => 'required|url',
            'cancel_url' => 'required|url',

            'customer' => 'required|array',
            'customer.name' => 'required|string|max:255',
            'customer.email' => 'required|email',
            'customer.mobile' => 'required|string|max:20',

            'shipping_info' => 'nullable|array',
            'shipping_info.address_one' => 'nullable|string|max:255',
            'shipping_info.address_two' => 'nullable|string|max:255',
            'shipping_info.area' => 'nullable|string|max:100',
            'shipping_info.city' => 'nullable|string|max:100',
            'shipping_info.sub_city' => 'nullable|string|max:100',
            'shipping_info.state' => 'nullable|string|max:100',
            'shipping_info.postcode' => 'nullable|string|max:20',
            'shipping_info.country' => 'nullable|string|max:100',
            'shipping_info.others' => 'nullable|string|max:255',

            'billing_info' => 'nullable|array',
            'billing_info.address_one' => 'nullable|string|max:255',
            'billing_info.address_two' => 'nullable|string|max:255',
            'billing_info.area' => 'nullable|string|max:100',
            'billing_info.city' => 'nullable|string|max:100',
            'billing_info.sub_city' => 'nullable|string|max:100',
            'billing_info.state' => 'nullable|string|max:100',
            'billing_info.postcode' => 'nullable|string|max:20',
            'billing_info.country' => 'nullable|string|max:100',
            'billing_info.others' => 'nullable|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $team = Team::where('public_key', $validated['public_key'])->where('status', 1)->first();

        $team = Team::where('public_key', $validated['public_key'])->first();

        if (!$team) {
            return response()->json([
                'error' => 'Invalid public key'
            ], 400);
        }

        $requestHost = $request->getHost();

        if ($team->website !== $requestHost) {
            return response()->json([
                'error' => 'Website does not match the request origin',
            ], 400);
        }

        if ($team->status === 1) {
            return response()->json([
                'error' => 'Website is inactive'
            ], 400);
        }

        // Create the Payment
        $payment = Payment::create([
            'model_id' => $team->id,
            'model_type' => Team::class,
            'method_currency' => $validated['currency'],
            'amount' => $validated['amount'],
            'detail' => $validated['details'],
            'trx' => Str::random(22),
            'status' => 0,
            'from_api' => true,
            'success_url' => $validated['success_url'],
            'failed_url' => $validated['cancel_url'],
            'customer' => $validated['customer'],
            'shipping_info' => $validated['shipping_info'] ?? [],
            'billing_info' => $validated['billing_info'] ?? [],
        ]);

        return response()->json(['status' => 'success', 'message' => 'Payment created successfully', 'data' => [
            'id' => $payment->trx,
            'url' => route('payment.index', $payment->trx),
        ]], 201);
    }

    public static function info(Request $request)
    {
        $rules = [
            'public_key' => 'required|string|max:50',
            'id' => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $team = Team::where('public_key', $validated['public_key'])->first();

        if (!$team) {
            return response()->json([
                'status' => 'error',
                'message' => 'Team not found'
            ], 404);
        }

        $payment = Payment::where('model_id', $team->id)->where('trx', $validated['id'])->first();

        if (!$payment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment not found'
            ], 404);
        }

        $status = 'unknown';

        switch ($payment->status) {
            case 0:
                $status = 'processing';
                break;
            case 1:
                $status = 'completed';
                break;
            case 2:
                $status = 'cancelled';
                break;
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'status' => $status,
                'currency' => $payment->method_currency,
                'amount' => Number::trim($payment->amount),
                'success_url' => $payment->success_url,
                'cancel_url' => $payment->failed_url,
                'customer' => $payment->customer,
                'shipping_info' => $payment->shipping_info,
                'billing_info' => $payment->billing_info
            ]
        ]);
    }

    public static function paymentDataUpdate($payment, $isCancel = false)
    {
        if ($payment->status == 0) {
            $payment->status = 1;
            $payment->save();
            if ($isCancel) {
                $payment->status = 2;
                $payment->save();
            }
        }
    }
    public static function subscription($payment_log)
    {
        $tenant = Team::find($payment_log->team_id);
        $plan = Plan::find($payment_log->payment->model_id);
        $payload = $payment_log->payload;
        $eventClass = $payload["event"] ?? null;
        if (!$eventClass || !class_exists($eventClass)) {
            return;
        }
        if ($eventClass === \App\Events\SubscribePlan::class) {
            $tenant->newPlanSubscription($plan->slug, $plan);
            Event::dispatch(new $eventClass([    // event App\Events\SubscribePlan
                "old" => null,
                "new" => $plan,
                "subscription" => $tenant->planSubscriptions()->first(),
                "team_id" => $tenant->id
            ]));
            return call_user_func(FilamentSubscriptions::getAfterSubscription(), [
                "old" => null,
                "new" => $plan,
                "subscription" => $tenant->planSubscriptions()->first(),
                "team_id" => $tenant->id
            ]);
        }
        if ($eventClass === \App\Events\RenewPlan::class) {
            $currentSubscription = $tenant->planSubscriptions()->first();
            $currentSubscription->canceled_at =  Carbon::parse($currentSubscription->cancels_at)->addDays(1);
            $currentSubscription->cancels_at = Carbon::parse($currentSubscription->cancels_at)->addDays(1);
            $currentSubscription->ends_at =  Carbon::parse($currentSubscription->cancels_at)->addDays(1);
            $currentSubscription->save();
            $currentSubscription->renew($plan);
            Event::dispatch(new $eventClass([
                "old" => $currentSubscription->plan,
                "new" => $plan,
                "subscription" => $currentSubscription,
                "team_id" => $tenant->id
            ]));
            return call_user_func(FilamentSubscriptions::getAfterRenew(), [
                "old" => $currentSubscription->plan,
                "new" => $plan,
                "subscription" => $currentSubscription,
                "team_id" => $tenant->id
            ]);
        }
        if ($eventClass === \App\Events\ChangePlan::class) {
            $currentSubscription = $tenant->planSubscriptions()->first();
            Event::dispatch(new $eventClass([
                "old" => $currentSubscription->plan,
                "new" => $plan,
                "subscription" => $currentSubscription,
                "team_id" => $tenant->id
            ]));
            $currentSubscription->changePlan($plan);
            return call_user_func(FilamentSubscriptions::getAfterChange(), [
                "old" => $currentSubscription->plan,
                "new" => $plan,
                "subscription" => $currentSubscription,
                "team_id" => $tenant->id
            ]);
        }
    }
}
