<?php

namespace App\Services;

use App\Models\RevenueForecast;
use App\Models\RevenueReportSnapshot;
use Carbon\Carbon;

class RevenueForecastService
{
    public function generate(string $forecastDate): void
    {
        $date = Carbon::parse($forecastDate)->toDateString();

        foreach (['KSH', 'USD'] as $currency) {
            $this->generateForCurrency($date, $currency);
        }
    }

    private function generateForCurrency(string $forecastDate, string $currency): void
    {
        $currentMonthStart = Carbon::parse($forecastDate)->startOfMonth()->toDateString();
        $currentMonthEnd = Carbon::parse($forecastDate)->endOfMonth()->toDateString();

        $actualRevenue = RevenueReportSnapshot::where('currency', $currency)
            ->whereDate('period_start', $currentMonthStart)
            ->whereDate('period_end', $currentMonthEnd)
            ->sum('billed_amount');

        $history = RevenueReportSnapshot::where('currency', $currency)
            ->selectRaw('period_start, SUM(billed_amount) as revenue')
            ->whereDate('period_start', '<', $currentMonthStart)
            ->groupBy('period_start')
            ->orderByDesc('period_start')
            ->limit(3)
            ->get();

        $averageRevenue = $history->count() > 0
            ? $history->avg('revenue')
            : $actualRevenue;

        $previousRevenue = $history->first()->revenue ?? 0;

        $growthRate = $previousRevenue > 0
            ? (($actualRevenue - $previousRevenue) / $previousRevenue) * 100
            : 0;

        $forecastRevenue = $averageRevenue;

        RevenueForecast::updateOrCreate(
            [
                'forecast_date' => $forecastDate,
                'currency' => $currency,
            ],
            [
                'actual_revenue' => $actualRevenue,
                'forecast_revenue' => $forecastRevenue,
                'growth_rate_percent' => $growthRate,
                'forecast_method' => '3_month_moving_average',
                'metadata' => [
                    'history_months' => $history->pluck('period_start')->values(),
                    'history_values' => $history->pluck('revenue')->values(),
                    'generated_at' => now()->toDateTimeString(),
                ],
            ]
        );
    }
}
