<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CurrencyService;

class SyncExchangeRates extends Command
{
    protected $signature = 'exchange:sync';
    protected $description = 'Sync daily exchange rates (USD → KES)';

    protected CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        parent::__construct();
        $this->currencyService = $currencyService;
    }

    public function handle(): void
    {
        $base = 'USD';
        $targets = ['KES']; // Add other currencies if needed

        foreach ($targets as $to) {
            try {
                $rate = $this->currencyService->refreshRate($base, $to);
                $this->info("Exchange rate {$base} → {$to} updated: {$rate}");
            } catch (\Exception $e) {
                $this->error("Failed to update {$base} → {$to}: {$e->getMessage()}");
            }
        }
    }
}
