<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
     'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY', 'AIzaSyB77eGv2kN5Lo-ZpD01-a277yCr2u-9Fto'),
    ],
'tevin' => [
    'device_ip' => env('TEVIN_DEVICE_IP', '209.182.239.212'),
    'device_port' => env('TEVIN_DEVICE_PORT', 1117),
    'sender_id' => env('TEVIN_SENDER_ID', '7b46fe6b518258a62e72'),
    'use_https' => env('TEVIN_USE_HTTPS', false),
],

'openai' => [
    'api_key' => env('OPENAI_API_KEY'),
    'organization' => env('OPENAI_ORGANIZATION'),
    'timeout' => env('OPENAI_TIMEOUT', 30),
],
];

