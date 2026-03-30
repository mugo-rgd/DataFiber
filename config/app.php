<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Branding
    |--------------------------------------------------------------------------
    */

    'name'        => env('APP_NAME', 'Dark Fibre CRM'),
    'description' => env('APP_DESCRIPTION', 'A complete CRM for fibre optic services'),
    'logo'        => env('APP_LOGO', '/images/logo.png'),
    'version' => env('APP_VERSION', '1.0.0'),
    /*
    |--------------------------------------------------------------------------
    | Environment & Debug
    |--------------------------------------------------------------------------
    */

    'env'   => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL & Domains
    |--------------------------------------------------------------------------
    */

    'url'           => env('APP_URL', 'http://localhost'),
    'admin_url'     => env('APP_ADMIN_URL', 'http://admin.localhost'),
    'customer_url'  => env('APP_CUSTOMER_URL', 'http://customer.localhost'),

    /*
    |--------------------------------------------------------------------------
    | Timezone & Locale
    |--------------------------------------------------------------------------
    */

    'timezone'        => env('APP_TIMEZONE', 'UTC'),
    'locale'          => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale'    => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Keys
    |--------------------------------------------------------------------------
    */

    'cipher'        => 'AES-256-CBC',
    'key'           => env('APP_KEY'),
    'previous_keys' => [
        ...array_filter(explode(',', (string) env('APP_PREVIOUS_KEYS', ''))),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode
    |--------------------------------------------------------------------------
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store'  => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    */

    'features' => [
        'multi_tenancy' => env('FEATURE_MULTI_TENANCY', false),
        'beta_portal'   => env('FEATURE_BETA_PORTAL', false),
        'dark_mode'     => env('FEATURE_DARK_MODE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Developer Settings
    |--------------------------------------------------------------------------
    */

    'developer' => [
        'show_debug_toolbar' => env('DEV_DEBUG_TOOLBAR', false),
        'log_sql_queries'    => env('DEV_LOG_SQL', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Company Information
    |--------------------------------------------------------------------------
    */

    'company' => [
        'name'    => env('COMPANY_NAME', 'Kenya Power and Lighting Pty'),
        'address' => env('COMPANY_ADDRESS', ''),
        'city'    => env('COMPANY_CITY', 'Nairobi'),
        'zip'     => env('COMPANY_ZIP', ''),
        'phone'   => env('COMPANY_PHONE', ''),
        'email'   => env('COMPANY_EMAIL', ''),
        'website' => env('COMPANY_WEBSITE', ''),
        'tax_id'  => env('COMPANY_TAX_ID', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Bank Information
    |--------------------------------------------------------------------------
    */

    'bank' => [
        'name'           => env('BANK_NAME', 'Cooperative Bank'),
        'account_name'   => env('BANK_ACCOUNT_NAME', 'Kenya Power and Lighting Pty'),
        'account_number' => env('BANK_ACCOUNT_NUMBER', '0000000000'),
        'routing_number' => env('BANK_ROUTING_NUMBER', ''),
        'iban'           => env('BANK_IBAN', ''),
        'swift'          => env('BANK_SWIFT', ''),
    ],

];
