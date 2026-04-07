<?php

namespace App\Services;

use App\Models\FinancialParameter;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class FinancialParameterSyncService
{
    /**
     * Sync a financial parameter to settings table
     */
    public static function syncToSettings(FinancialParameter $parameter)
    {
        try {
            $mapping = self::getParameterMapping();

            if (!isset($mapping[$parameter->parameter_name])) {
                return false;
            }

            $settingsMap = $mapping[$parameter->parameter_name];

            foreach ($settingsMap as $settingKey => $transform) {
                $value = $transform($parameter->parameter_value);

                Setting::set(
                    $settingKey,
                    $value,
                    self::getSettingType($settingKey),
                    self::getSettingDescription($settingKey)
                );
            }

            Log::info('Synced financial parameter to settings', [
                'parameter_name' => $parameter->parameter_name,
                'parameter_value' => $parameter->parameter_value
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to sync financial parameter to settings: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync a setting to financial parameters table
     */
    public static function syncToFinancialParameters(Setting $setting)
    {
        try {
            $reverseMapping = self::getReverseMapping();

            if (!isset($reverseMapping[$setting->key])) {
                return false;
            }

            $parameterConfig = $reverseMapping[$setting->key];

            $parameter = FinancialParameter::updateOrCreate(
                [
                    'parameter_name' => $parameterConfig['parameter_name'],
                    'currency_code' => $parameterConfig['currency_code'] ?? null,
                    'country_code' => $parameterConfig['country_code'] ?? 'KEN',
                ],
                [
                    'parameter_value' => $parameterConfig['transform'](Setting::get($setting->key)),
                    'effective_from' => now()->startOfDay(),
                    'effective_to' => null,
                    'description' => $parameterConfig['description'],
                    'created_by' => auth()->id() ?? 1,
                    'updated_by' => auth()->id() ?? 1,
                ]
            );

            Log::info('Synced setting to financial parameters', [
                'setting_key' => $setting->key,
                'parameter_name' => $parameter->parameter_name,
                'parameter_value' => $parameter->parameter_value
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to sync setting to financial parameters: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get mapping from financial_parameters to settings
     */
    private static function getParameterMapping()
    {
        return [
            'vat_rate' => [
                'default_vat_rate' => function($value) {
                    return $value * 100; // Convert decimal to percentage
                },
            ],
            'kes_to_usd' => [
                'usd_to_kes_rate' => function($value) {
                    return 1 / $value; // Inverse exchange rate
                },
                'kes_to_usd_rate' => function($value) {
                    return $value;
                },
                'exchange_rate_last_updated' => function($value) {
                    return now()->toDateTimeString();
                },
            ],
            'late_payment_interest_rate' => [
                'late_payment_interest_rate' => function($value) {
                    return $value * 100; // Convert to percentage
                },
            ],
            'early_payment_discount_rate' => [
                'early_payment_discount' => function($value) {
                    return $value * 100; // Convert to percentage
                },
            ],
            'withholding_tax_services' => [
                'default_withholding_tax_rate' => function($value) {
                    return $value * 100; // Convert to percentage
                },
            ],
            'standard_payment_terms_days' => [
                'standard_payment_terms_days' => function($value) {
                    return $value;
                },
            ],
            'minimum_invoice_amount' => [
                'minimum_invoice_amount' => function($value) {
                    return $value;
                },
            ],
            'grace_period_days' => [
                'grace_period_days' => function($value) {
                    return $value;
                },
            ],
        ];
    }

    /**
     * Get reverse mapping from settings to financial_parameters
     */
    private static function getReverseMapping()
    {
        return [
            'default_vat_rate' => [
                'parameter_name' => 'vat_rate',
                'currency_code' => 'USD',
                'country_code' => 'KEN',
                'description' => 'Value Added Tax Rate for Kenya',
                'transform' => function($value) {
                    return $value / 100; // Convert percentage to decimal
                },
            ],
            'usd_to_kes_rate' => [
                'parameter_name' => 'kes_to_usd',
                'currency_code' => 'KES',
                'country_code' => 'KEN',
                'description' => 'Kenyan Shilling to US Dollar exchange rate',
                'transform' => function($value) {
                    return 1 / $value; // Inverse exchange rate
                },
            ],
            'late_payment_interest_rate' => [
                'parameter_name' => 'late_payment_interest_rate',
                'currency_code' => null,
                'country_code' => 'KEN',
                'description' => 'Late payment interest rate (2% per month)',
                'transform' => function($value) {
                    return $value / 100; // Convert percentage to decimal
                },
            ],
            'early_payment_discount' => [
                'parameter_name' => 'early_payment_discount_rate',
                'currency_code' => null,
                'country_code' => 'KEN',
                'description' => 'Early payment discount rate',
                'transform' => function($value) {
                    return $value / 100; // Convert percentage to decimal
                },
            ],
            'default_withholding_tax_rate' => [
                'parameter_name' => 'withholding_tax_services',
                'currency_code' => null,
                'country_code' => 'KEN',
                'description' => 'Withholding tax rate for services',
                'transform' => function($value) {
                    return $value / 100; // Convert percentage to decimal
                },
            ],
            'standard_payment_terms_days' => [
                'parameter_name' => 'standard_payment_terms_days',
                'currency_code' => null,
                'country_code' => 'KEN',
                'description' => 'Standard payment terms in days',
                'transform' => function($value) {
                    return $value;
                },
            ],
            'minimum_invoice_amount' => [
                'parameter_name' => 'minimum_invoice_amount',
                'currency_code' => 'USD',
                'country_code' => 'KEN',
                'description' => 'Minimum invoice amount in USD',
                'transform' => function($value) {
                    return $value;
                },
            ],
            'grace_period_days' => [
                'parameter_name' => 'grace_period_days',
                'currency_code' => null,
                'country_code' => 'KEN',
                'description' => 'Grace period in days before late fees apply',
                'transform' => function($value) {
                    return $value;
                },
            ],
        ];
    }

    /**
     * Get setting type based on key
     */
    private static function getSettingType($key)
    {
        $types = [
            'default_vat_rate' => 'decimal',
            'usd_to_kes_rate' => 'decimal',
            'kes_to_usd_rate' => 'decimal',
            'exchange_rate_last_updated' => 'string',
            'late_payment_interest_rate' => 'decimal',
            'early_payment_discount' => 'decimal',
            'default_withholding_tax_rate' => 'decimal',
            'standard_payment_terms_days' => 'integer',
            'minimum_invoice_amount' => 'decimal',
            'grace_period_days' => 'integer',
        ];

        return $types[$key] ?? 'string';
    }

    /**
     * Get setting description based on key
     */
    private static function getSettingDescription($key)
    {
        $descriptions = [
            'default_vat_rate' => 'Default VAT rate percentage',
            'usd_to_kes_rate' => 'Current USD to KES exchange rate',
            'kes_to_usd_rate' => 'Current KES to USD exchange rate',
            'exchange_rate_last_updated' => 'Last time exchange rate was updated',
            'late_payment_interest_rate' => 'Late payment interest rate percentage per month',
            'early_payment_discount' => 'Early payment discount percentage',
            'default_withholding_tax_rate' => 'Default withholding tax rate percentage',
            'standard_payment_terms_days' => 'Standard payment terms in days',
            'minimum_invoice_amount' => 'Minimum invoice amount in USD',
            'grace_period_days' => 'Grace period in days before late fees apply',
        ];

        return $descriptions[$key] ?? null;
    }

    /**
     * Sync all financial parameters to settings
     */
    public static function syncAllToSettings()
    {
        $parameters = FinancialParameter::whereNull('effective_to')
            ->orWhere('effective_to', '>=', now())
            ->get();

        $synced = 0;
        foreach ($parameters as $parameter) {
            if (self::syncToSettings($parameter)) {
                $synced++;
            }
        }

        Log::info("Synced {$synced} financial parameters to settings");

        return $synced;
    }

    /**
     * Sync all settings to financial parameters
     */
    public static function syncAllToFinancialParameters()
    {
        $settings = Setting::all();

        $synced = 0;
        foreach ($settings as $setting) {
            if (self::syncToFinancialParameters($setting)) {
                $synced++;
            }
        }

        Log::info("Synced {$synced} settings to financial parameters");

        return $synced;
    }
}
