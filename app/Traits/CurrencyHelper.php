<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait CurrencyHelper
{
    /**
     * Get current exchange rate (USD to KES)
     */
    protected function getExchangeRate(): float
    {
        // Cache for 1 hour to avoid hitting API too often
        return Cache::remember('usd_to_kes_rate', 3600, function () {
            // First try to get from database settings
            $rate = DB::table('settings')
                ->where('key', 'usd_to_kes_rate')
                ->value('value');

            if ($rate && (float) $rate > 0) {
                return (float) $rate;
            }

            // Fallback to API
            try {
                $response = Http::timeout(5)->get('https://api.exchangerate-api.com/v4/latest/USD');
                if ($response->successful()) {
                    $kesRate = $response->json()['rates']['KES'] ?? 130;

                    // Update database for next time
                    DB::table('settings')->updateOrInsert(
                        ['key' => 'usd_to_kes_rate'],
                        ['value' => $kesRate, 'updated_at' => now()]
                    );

                    return (float) $kesRate;
                }
            } catch (\Exception $e) {
                Log::warning('Could not fetch exchange rate from API: ' . $e->getMessage());
            }

            // Default fallback
            return 130.00;
        });
    }

    /**
     * Format currency amount
     */
    protected function formatCurrency(float $amount, string $currency = 'KSH'): string
    {
        if ($currency === 'USD') {
            return '$' . number_format($amount, 2);
        }
        return 'KSh ' . number_format($amount, 2);
    }

    /**
     * Convert amount between currencies
     */
    protected function convertCurrency(float $amount, string $from, string $to): float
    {
        if ($from === $to) {
            return $amount;
        }

        $rate = $this->getExchangeRate();

        if ($from === 'USD' && $to === 'KSH') {
            return $amount * $rate;
        }

        if ($from === 'KSH' && $to === 'USD') {
            return $amount / $rate;
        }

        return $amount;
    }

    /**
     * Get currency symbol
     */
    protected function getCurrencySymbol(string $currency = 'KSH'): string
    {
        return $currency === 'USD' ? '$' : 'KSh ';
    }
}
