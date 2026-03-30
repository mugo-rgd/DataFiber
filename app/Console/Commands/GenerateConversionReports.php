<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ConversionData;
use Illuminate\Support\Facades\DB;

class GenerateConversionReports extends Command
{
    protected $signature = 'reports:generate';
    protected $description = 'Generate reports for conversion data';

    public function handle()
    {
        $this->info('Generating conversion data reports...');

        $this->newLine();
        $this->line('=== Summary Report ===');

        // Basic summary
        $summary = [
            'Total Records' => ConversionData::count(),
            'Total Customers' => DB::table('conversion_data')->distinct('customer_name')->count(),
            'Total Monthly Value (USD)' => ConversionData::sum('monthly_link_value_usd'),
            'Total Contract Value (USD)' => ConversionData::sum('total_contract_value_usd'),
            'Average Contract Duration' => ConversionData::avg('contract_duration_yrs'),
        ];

        $this->table(['Metric', 'Value'], array_map(function($key, $value) {
            return [$key, $value];
        }, array_keys($summary), array_values($summary)));

        $this->newLine();
        $this->line('=== Top 10 Customers by Contract Value ===');

        $topCustomers = ConversionData::select('customer_name', DB::raw('SUM(total_contract_value_usd) as total_contract_value'))
            ->whereNotNull('total_contract_value_usd')
            ->groupBy('customer_name')
            ->orderBy('total_contract_value', 'desc')
            ->limit(10)
            ->get();

        $this->table(['Customer', 'Total Contract Value (USD)'], $topCustomers->map(function($item) {
            return [$item->customer_name, number_format($item->total_contract_value, 2)];
        })->toArray());

        $this->newLine();
        $this->line('=== Link Class Distribution ===');

        $linkClasses = ConversionData::select('link_class', DB::raw('COUNT(*) as count'))
            ->whereNotNull('link_class')
            ->groupBy('link_class')
            ->orderBy('count', 'desc')
            ->get();

        $this->table(['Link Class', 'Count'], $linkClasses->map(function($item) {
            return [$item->link_class, $item->count];
        })->toArray());

        $this->info('Reports generated successfully!');
    }
}
