<?php
// config/customer.php

return [
    'guard' => 'customer',
    'providers' => [
        'customers' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],
];
