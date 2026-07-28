<?php

return [
    'secret_key' => env('STRIPE_SECRET', ''),
    'publishable_key' => env('STRIPE_KEY', ''),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),
];
