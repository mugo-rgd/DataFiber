<?php

declare(strict_types=1);

return [
    'default' => env('EXCHANGE_DRIVER', 'exchange_rate'),

    'services' => [
        'exchange_rate' => [
            'driver' => 'exchange_rate',
            'access_key' => env('EXCHANGE_RATE_ACCESS_KEY'),
        ],

        'currency_geo' => [
            'access_key' => env('CURRENCY_GEO_ACCESS_KEY'),
        ],

        'cache' => [
            'strategy' => env('EXCHANGE_RATES_CACHE_STRATEGY', 'frankfurter'),
            'ttl' => env('EXCHANGE_RATES_CACHE_TTL', 60 * 60 * 24),
            'key' => env('EXCHANGE_RATES_CACHE_KEY', 'cached_exchange_rates'),
            'store' => env('EXCHANGE_RATES_CACHE_STORE'),
        ],
    ],

    'features' => [
        'about_command' => true,
    ],
];
