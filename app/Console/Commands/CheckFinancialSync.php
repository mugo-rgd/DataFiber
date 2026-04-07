<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckFinancialSync extends Command
{
    protected $signature = 'financial:check-sync';
    protected $description = 'Check if financial_parameters and settings tables are in sync';

    public function handle()
    {
        $this->info('Checking financial parameters sync status...');
        $this->newLine();

        // Check exchange rate sync
        $kesToUsd = DB::table('financial_parameters')
            ->where('parameter_name', 'kes_to_usd')
            ->where('effective_from', '<=', now())
            ->where(function($q) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', now());
            })
            ->first();

        $usdToKesSetting = DB::table('settings')->where('key', 'usd_to_kes_rate')->first();

        $this->info('Exchange Rate Sync:');
        $this->table(
            ['Source', 'Parameter', 'Value'],
            [
                ['Financial Parameters', 'kes_to_usd', $kesToUsd->parameter_value ?? 'N/A'],
                ['Settings', 'usd_to_kes_rate', $usdToKesSetting->value ?? 'N/A'],
            ]
        );

        if ($kesToUsd && $usdToKesSetting) {
            $calculatedUsdToKes = 1 / $kesToUsd->parameter_value;
            if (abs($calculatedUsdToKes - $usdToKesSetting->value) < 0.01) {
                $this->info('✅ Exchange rates are in sync!');
            } else {
                $this->error('❌ Exchange rates are OUT of sync!');
                $this->warn("Financial Parameters: 1 USD = " . (1/$kesToUsd->parameter_value) . " KES");
                $this->warn("Settings: 1 USD = {$usdToKesSetting->value} KES");
            }
        }

        $this->newLine();

        // Check VAT rate sync
        $vatRate = DB::table('financial_parameters')
            ->where('parameter_name', 'vat_rate')
            ->where('effective_from', '<=', now())
            ->where(function($q) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', now());
            })
            ->first();

        $vatSetting = DB::table('settings')->where('key', 'default_vat_rate')->first();

        $this->info('VAT Rate Sync:');
        $this->table(
            ['Source', 'Parameter', 'Value (%)'],
            [
                ['Financial Parameters', 'vat_rate', ($vatRate->parameter_value ?? 0) * 100],
                ['Settings', 'default_vat_rate', $vatSetting->value ?? 'N/A'],
            ]
        );

        if ($vatRate && $vatSetting) {
            $calculatedVat = $vatRate->parameter_value * 100;
            if (abs($calculatedVat - $vatSetting->value) < 0.01) {
                $this->info('✅ VAT rates are in sync!');
            } else {
                $this->error('❌ VAT rates are OUT of sync!');
            }
        }

        $this->newLine();
        $this->info('Sync check completed!');
    }
}
