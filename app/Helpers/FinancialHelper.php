<?php

namespace App\Helpers;

use App\Models\FinancialParameter;
use App\Models\Setting;

class FinancialHelper
{
    /**
     * Get current VAT rate
     */
    public static function getVatRate($currency = null)
    {
        return FinancialParameter::getCurrentValue('vat_rate', $currency) ?? 0.16;
    }

    /**
     * Get current exchange rate (USD to KES)
     */
    public static function getExchangeRate()
    {
        return Setting::getExchangeRate();
    }

    /**
     * Convert amount between currencies
     */
    public static function convertCurrency($amount, $from, $to)
    {
        if ($from === $to) {
            return $amount;
        }

        $rate = FinancialParameter::getExchangeRate($from, $to);
        return $amount * ($rate ?? 1);
    }

    /**
     * Calculate total with VAT
     */
    public static function calculateTotalWithVat($amount, $currency = 'USD')
    {
        $vatRate = self::getVatRate($currency);
        return $amount * (1 + $vatRate);
    }

    /**
     * Get payment terms in days
     */
    public static function getPaymentTerms()
    {
        return Setting::get('standard_payment_terms_days', 30);
    }

    /**
     * Get late payment interest rate
     */
    public static function getLateInterestRate()
    {
        return FinancialParameter::getCurrentValue('late_payment_interest_rate') ?? 0.02;
    }
}
