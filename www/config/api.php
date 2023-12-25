<?php

return [
    'throttle' => [
        'enabled' => env('API_RATE_LIMITING_ENABLED', true),
        'attempts' => env('API_RATE_LIMITING_ATTEMPTS', 1000),
        'decay_seconds' => env('API_RATE_LIMITING_DECAY_SECONDS', 60),
    ],
];
