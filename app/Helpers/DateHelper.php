<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Get current financial year (Kenya: July to June)
     */
    public static function getCurrentFinancialYear()
    {
        $now = Carbon::now();
        $year = $now->year;

        if ($now->month >= 7) {
            return "{$year}/" . ($year + 1);
        }

        return ($year - 1) . "/{$year}";
    }

    /**
     * Get current quarter based on Kenya financial year
     */
    public static function getCurrentQuarter()
    {
        $month = Carbon::now()->month;

        return match(true) {
            $month >= 7 && $month <= 9 => 'Q1',
            $month >= 10 && $month <= 12 => 'Q2',
            $month >= 1 && $month <= 3 => 'Q3',
            default => 'Q4',
        };
    }

    /**
     * Get quarter date range
     */
    public static function getQuarterDateRange($quarter, $financialYear)
    {
        [$startYear, $endYear] = explode('/', $financialYear);

        return match($quarter) {
            'Q1' => ['start' => "{$startYear}-07-01", 'end' => "{$startYear}-09-30"],
            'Q2' => ['start' => "{$startYear}-10-01", 'end' => "{$startYear}-12-31"],
            'Q3' => ['start' => "{$endYear}-01-01", 'end' => "{$endYear}-03-31"],
            'Q4' => ['start' => "{$endYear}-04-01", 'end' => "{$endYear}-06-30"],
            default => null,
        };
    }
}
