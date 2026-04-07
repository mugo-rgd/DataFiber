<?php

namespace App\Models;

use App\Services\FinancialParameterSyncService;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];

    protected $casts = [
        'value' => 'json',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // After creating a setting, sync to financial parameters
        static::created(function ($setting) {
            FinancialParameterSyncService::syncToFinancialParameters($setting);
        });

        // After updating a setting, sync to financial parameters
        static::updated(function ($setting) {
            // Only sync if value changed
            if ($setting->isDirty('value')) {
                FinancialParameterSyncService::syncToFinancialParameters($setting);
            }
        });
    }

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return match($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting->value,
            'decimal' => (float) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    public static function set(string $key, $value, string $type = 'string', ?string $description = null)
    {
        if (is_array($value)) {
            $value = json_encode($value);
            $type = 'json';
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
            $type = 'boolean';
        } elseif (is_int($value)) {
            $value = (string) $value;
            $type = 'integer';
        } elseif (is_float($value)) {
            $value = (string) $value;
            $type = 'decimal';
        }

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'description' => $description]
        );
    }

    /**
     * Get exchange rate from settings
     */
    public static function getExchangeRate()
    {
        return self::get('usd_to_kes_rate', 130.04);
    }

    /**
     * Update exchange rate
     */
    public static function updateExchangeRate($rate, $source = 'manual')
    {
        self::set('usd_to_kes_rate', (float) $rate, 'decimal');
        self::set('kes_to_usd_rate', 1 / (float) $rate, 'decimal');
        self::set('exchange_rate_source', $source, 'string');
        self::set('exchange_rate_last_updated', now()->toDateTimeString(), 'string');

        return true;
    }
}
