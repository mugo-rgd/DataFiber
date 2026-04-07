<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FinancialParametersSeeder extends Seeder
{
    public function run()
    {
        // Clear existing records (optional - be careful in production)
        // DB::table('financial_parameters')->truncate();

        $financialParameters = [
            // VAT Rates
            [
                'parameter_name' => 'vat_rate',
                'parameter_value' => 0.160000,
                'effective_from' => '2025-01-01',
                'effective_to' => null,
                'currency_code' => 'USD',
                'country_code' => 'KEN',
                'description' => 'Value Added Tax Rate for Kenya',
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parameter_name' => 'vat_rate_ksh',
                'parameter_value' => 0.160000,
                'effective_from' => '2025-01-01',
                'effective_to' => null,
                'currency_code' => 'KSH',
                'country_code' => 'KEN',
                'description' => 'Value Added Tax Rate for Kenya (KSH)',
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Exchange Rates
            [
                'parameter_name' => 'kes_to_usd',
                'parameter_value' => 130.040000,
                'effective_from' => '2026-04-01',
                'effective_to' => null,
                'currency_code' => 'KES',
                'country_code' => 'KEN',
                'description' => 'Kenyan Shilling to US Dollar exchange rate',
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parameter_name' => 'usd_to_kes',
                'parameter_value' => 0.007690,
                'effective_from' => '2026-04-01',
                'effective_to' => null,
                'currency_code' => 'USD',
                'country_code' => 'KEN',
                'description' => 'US Dollar to Kenyan Shilling exchange rate (1/130.04)',
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Interest Rates
            [
                'parameter_name' => 'late_payment_interest_rate',
                'parameter_value' => 0.020000,
                'effective_from' => '2025-01-01',
                'effective_to' => null,
                'currency_code' => null,
                'country_code' => 'KEN',
                'description' => 'Late payment interest rate (2% per month)',
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parameter_name' => 'early_payment_discount_rate',
                'parameter_value' => 0.020000,
                'effective_from' => '2025-01-01',
                'effective_to' => null,
                'currency_code' => null,
                'country_code' => 'KEN',
                'description' => 'Early payment discount rate (2% discount)',
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Withholding Tax Rates
            [
                'parameter_name' => 'withholding_tax_services',
                'parameter_value' => 0.050000,
                'effective_from' => '2025-01-01',
                'effective_to' => null,
                'currency_code' => null,
                'country_code' => 'KEN',
                'description' => 'Withholding tax rate for services (5%)',
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parameter_name' => 'withholding_tax_interest',
                'parameter_value' => 0.150000,
                'effective_from' => '2025-01-01',
                'effective_to' => null,
                'currency_code' => null,
                'country_code' => 'KEN',
                'description' => 'Withholding tax rate for interest (15%)',
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Billing Parameters
            [
                'parameter_name' => 'minimum_invoice_amount',
                'parameter_value' => 100.000000,
                'effective_from' => '2025-01-01',
                'effective_to' => null,
                'currency_code' => 'USD',
                'country_code' => 'KEN',
                'description' => 'Minimum invoice amount in USD',
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parameter_name' => 'minimum_invoice_amount_ksh',
                'parameter_value' => 1000.000000,
                'effective_from' => '2025-01-01',
                'effective_to' => null,
                'currency_code' => 'KSH',
                'country_code' => 'KEN',
                'description' => 'Minimum invoice amount in KSH',
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Credit Terms
            [
                'parameter_name' => 'standard_payment_terms_days',
                'parameter_value' => 30.000000,
                'effective_from' => '2025-01-01',
                'effective_to' => null,
                'currency_code' => null,
                'country_code' => 'KEN',
                'description' => 'Standard payment terms in days',
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parameter_name' => 'grace_period_days',
                'parameter_value' => 7.000000,
                'effective_from' => '2025-01-01',
                'effective_to' => null,
                'currency_code' => null,
                'country_code' => 'KEN',
                'description' => 'Grace period in days before late fees apply',
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Historical Exchange Rates
            [
                'parameter_name' => 'kes_to_usd_historical',
                'parameter_value' => 129.200000,
                'effective_from' => '2025-10-01',
                'effective_to' => '2025-10-31',
                'currency_code' => 'KES',
                'country_code' => 'KEN',
                'description' => 'October 2025 exchange rate',
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parameter_name' => 'kes_to_usd_historical',
                'parameter_value' => 130.040000,
                'effective_from' => '2026-04-01',
                'effective_to' => null,
                'currency_code' => 'KES',
                'country_code' => 'KEN',
                'description' => 'Current exchange rate from April 2026',
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($financialParameters as $parameter) {
            // Check if record exists to avoid duplicates
            $exists = DB::table('financial_parameters')
                ->where('parameter_name', $parameter['parameter_name'])
                ->where('effective_from', $parameter['effective_from'])
                ->exists();

            if (!$exists) {
                DB::table('financial_parameters')->insert($parameter);
            }
        }

        $this->command->info('Financial parameters seeded successfully!');
    }
}
