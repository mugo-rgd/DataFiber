<?php

if (!function_exists('formatCurrency')) {

    /**
     * Format money values with currency support
     *
     * Examples:
     * formatCurrency(25000, 'KES')
     * => KES 25,000.00
     *
     * formatCurrency(1200, 'USD')
     * => $ 1,200.00
     */
    function formatCurrency($amount, $currency = 'KES', $decimals = 2)
    {
        // Prevent null values
        $amount = $amount ?? 0;

        // Normalize currency code
        $currency = strtoupper(trim($currency));

        // Currency display configuration
        $currencies = [
            'KES' => [
                'symbol' => 'KES',
                'position' => 'before'
            ],

            'USD' => [
                'symbol' => '$',
                'position' => 'before'
            ],

            // Future currencies can easily be added
            'EUR' => [
                'symbol' => '€',
                'position' => 'before'
            ],

            'GBP' => [
                'symbol' => '£',
                'position' => 'before'
            ],
        ];

        // Use configured currency or fallback
        $config = $currencies[$currency] ?? [
            'symbol' => $currency,
            'position' => 'before'
        ];

        $formattedAmount = number_format(
            (float)$amount,
            $decimals
        );

        return $config['position'] === 'before'
            ? "{$config['symbol']} {$formattedAmount}"
            : "{$formattedAmount} {$config['symbol']}";
    }
}


if (!function_exists('currencySymbol')) {

    /**
     * Return currency symbol only
     *
     * Example:
     * currencySymbol('USD')
     * => $
     */
    function currencySymbol($currency = 'KES')
    {
        $symbols = [
            'KES' => 'KES',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
        ];

        return $symbols[strtoupper($currency)] ?? $currency;
    }
}


if (!function_exists('currencyCode')) {

    /**
     * Normalize currency code
     */
    function currencyCode($currency = 'KES')
    {
        return strtoupper(trim($currency));
    }
}
