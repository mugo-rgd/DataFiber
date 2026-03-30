<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConversionData;
use Illuminate\Support\Facades\DB;

class ConversionDataSeeder extends Seeder
{
    public function run()
    {
        // First, let's parse the text file content
        $content = file_get_contents(base_path('database/seeders/link_inventory_data.txt'));
        $lines = explode("\n", $content);

        $headers = [];
        $data = [];

        foreach ($lines as $index => $line) {
            // Skip empty lines
            if (empty(trim($line))) {
                continue;
            }

            // Split by tabs
            $row = preg_split('/\t+/', $line);

            // First non-empty line is headers
            if ($index === 0) {
                $headers = $this->cleanHeaders($row);
                continue;
            }

            // Process data rows
            if (count($row) >= 14) {
                $rowData = [];
                foreach ($headers as $i => $header) {
                    $rowData[$header] = isset($row[$i]) ? $this->cleanValue($row[$i]) : null;
                }
                $data[] = $rowData;
            }
        }

        // Insert in chunks to avoid memory issues
        $chunks = array_chunk($data, 100);

        foreach ($chunks as $chunk) {
            ConversionData::insert($chunk);
        }

        $this->command->info('Seeded ' . count($data) . ' conversion data records.');
    }

    private function cleanHeaders($headers)
    {
        $cleanHeaders = [];
        $headerMap = [
            'CUSTOMER REF.' => 'customer_ref',
            'CUSTOMER ID' => 'customer_id',
            'NAME OF THE CUSTOMERS' => 'customer_name',
            'ROUTE NAME' => 'route_name',
            'LINKS NAME' => 'links_name',
            'CORE/CORES LEASED' => 'cores_leased',
            'BANDWIDTH' => 'bandwidth',
            'DISTANCE (KM)' => 'distance_km',
            'PRICE/ CORE/ PER KM/ PER MONTH (USD)' => 'price_per_core_per_km_per_month_usd',
            'MONTHLY LINK VALUE (USD)' => 'monthly_link_value_usd',
            'MONTHLY LINK (KES)' => 'monthly_link_kes',
            'LINK CLASS' => 'link_class',
            'CONTRACT DURATION (YRS)' => 'contract_duration_yrs',
            'TOTAL CONTRACT VALUE (USD)' => 'total_contract_value_usd',
            'TOTAL CONTRACT VALUE (KES)' => 'total_contract_value_kes'
        ];

        foreach ($headers as $header) {
            $cleanHeader = trim($header);
            $cleanHeaders[] = $headerMap[$cleanHeader] ?? strtolower(str_replace([' ', '(', ')'], ['_', '', ''], $cleanHeader));
        }

        return $cleanHeaders;
    }

    private function cleanValue($value)
    {
        $value = trim($value);

        // Remove quotes and commas from numeric values
        $value = str_replace(['"', ',', ' '], '', $value);

        // Convert empty strings to null
        if ($value === '' || $value === '""') {
            return null;
        }

        // Check if it's numeric
        if (is_numeric($value)) {
            return $value;
        }

        return $value;
    }
}
