<?php

return [
    "drivers" => [
        \App\Services\Drivers\Paypal::class,
        \App\Services\Drivers\StripeV3::class,
    ],
    "path" => "App\\Services\\Drivers"
];
