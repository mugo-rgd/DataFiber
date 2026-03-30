<?php

namespace App\Services;

use Worksome\Exchange\Facades\Exchange;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class CurrencyService
{
    /**
     * Default cache duration in seconds (1 hour)
     */
    protected int $cacheDuration = 3600;

    /**
     * Get the exchange rate from $from to $to
     *
     * @throws RuntimeException
     */
    public function rate(string $from, string $to): float
    {
        $cacheKey = "exchange_rate_{$from}_{$to}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($from, $to) {
            try {
                $rates = Exchange::getRates($from, [$to]);

                // Convert Worksome Rates object to array
                $payload = json_decode(json_encode($rates), true);

                if (!isset($payload['rates'][$to]) || !is_numeric($payload['rates'][$to])) {
                    throw new RuntimeException(
                        "CurrencyService: Rate from {$from} to {$to} not found. Payload: " . json_encode($payload)
                    );
                }

                return (float) $payload['rates'][$to];
            } catch (\Throwable $e) {
                // Log the exception and rethrow as a runtime exception
                \Log::error("CurrencyService: Failed to fetch rate from {$from} to {$to}.", [
                    'error' => $e->getMessage(),
                ]);

                throw new RuntimeException("Could not get exchange rate for {$from}→{$to}.");
            }
        });
    }

    /**
     * Convert an amount from $from to $to
     */
    public function convert(float $amount, string $from, string $to): float
    {
        return round($amount * $this->rate($from, $to), 2);
    }

    /**
     * Convert a batch of amounts from $from to $to
     */
    public function convertBatch(array $amounts, string $from, string $to): array
    {
        $rate = $this->rate($from, $to);

        return array_map(fn($amount) => round($amount * $rate, 2), $amounts);
    }

    /**
     * Force refresh cached rate
     */
    public function refreshRate(string $from, string $to): float
    {
        $cacheKey = "exchange_rate_{$from}_{$to}";
        Cache::forget($cacheKey);

        return $this->rate($from, $to);
    }
}
