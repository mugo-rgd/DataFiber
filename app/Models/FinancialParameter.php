<?php

namespace App\Models;

use App\Services\FinancialParameterSyncService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FinancialParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'parameter_name',
        'parameter_value',
        'effective_from',
        'effective_to',
        'currency_code',
        'country_code',
        'description',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'parameter_value' => 'decimal:6',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    // Common parameter names as constants
    const VAT_RATE = 'vat_rate';
    const KES_TO_USD = 'kes_to_usd';
    const EUR_TO_USD = 'eur_to_usd';
    const GBP_TO_USD = 'gbp_to_usd';

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // After creating a financial parameter, sync to settings
        static::created(function ($parameter) {
            FinancialParameterSyncService::syncToSettings($parameter);
        });

        // After updating a financial parameter, sync to settings
        static::updated(function ($parameter) {
            // Only sync if value changed
            if ($parameter->isDirty('parameter_value')) {
                FinancialParameterSyncService::syncToSettings($parameter);
            }
        });
    }

    /**
     * Scope for active parameters at a given date
     */
    public function scopeActiveAt(Builder $query, $date = null)
    {
        $date = $date ?: now()->format('Y-m-d');

        return $query->where('effective_from', '<=', $date)
            ->where(function($q) use ($date) {
                $q->where('effective_to', '>=', $date)
                  ->orWhereNull('effective_to');
            });
    }

    /**
     * Scope for specific parameter name
     */
    public function scopeForParameter(Builder $query, $parameterName)
    {
        return $query->where('parameter_name', $parameterName);
    }

    /**
     * Scope for specific currency
     */
    public function scopeForCurrency(Builder $query, $currencyCode)
    {
        return $query->where('currency_code', $currencyCode);
    }

    /**
     * Scope for Kenya
     */
    public function scopeForKenya(Builder $query)
    {
        return $query->where('country_code', 'KEN');
    }

    /**
     * Get current VAT rate for Kenya
     */
    public static function getCurrentVatRate($date = null)
    {
        $parameter = self::forParameter(self::VAT_RATE)
            ->forKenya()
            ->activeAt($date)
            ->first();

        return $parameter ? $parameter->parameter_value : 0.16;
    }

    /**
     * Get current exchange rate for a currency to USD
     */
    public static function getCurrentExchangeRate($currencyCode, $date = null)
    {
        $parameterName = strtolower($currencyCode) . '_to_usd';

        $parameter = self::forParameter($parameterName)
            ->forCurrency($currencyCode)
            ->activeAt($date)
            ->first();

        return $parameter ? $parameter->parameter_value : null;
    }

    /**
     * Get USD to KES exchange rate
     */
    public static function getUsdToKesRate($date = null)
    {
        $kesToUsd = self::getCurrentExchangeRate('KES', $date);
        return $kesToUsd ? 1 / $kesToUsd : 130.04;
    }

    /**
     * Relationship with creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship with updater
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
