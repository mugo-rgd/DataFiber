<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ConversionData;
use Illuminate\Support\Facades\DB;

class ImportConversionData extends Command
{
    protected $signature = 'import:conversion-data';
    protected $description = 'Import data from tab-separated file';

    public function handle()
    {
        $path = storage_path('app/link_inventory_per_customer.txt');

        if (!file_exists($path)) {
            $this->error("File not found!");
            return 1;
        }

        $this->info("Reading file: {$path}");

        // Clear table
        DB::table('conversion_data')->truncate();

        // Read file with proper encoding handling
        $handle = fopen($path, 'r');
        if (!$handle) {
            $this->error("Cannot open file!");
            return 1;
        }

        $imported = 0;
        $lineNumber = 0;

        // Read and skip header
        $header = fgets($handle);
        $lineNumber++;

        $this->info("Processing data...");

        while (($line = fgets($handle)) !== false) {
            $lineNumber++;
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            // Fix encoding issues - convert to UTF-8 and remove invalid characters
            $line = $this->fixEncoding($line);

            // Parse the line
            $record = $this->parseLine($line);

            if ($record) {
                try {
                    ConversionData::create($record);
                    $imported++;

                    if ($imported % 100 === 0) {
                        $this->info("Imported {$imported} records...");
                    }
                } catch (\Exception $e) {
                    $this->error("Error at line {$lineNumber}: " . $e->getMessage());
                    $this->error("Problematic line: " . substr($line, 0, 100));
                    // Continue with next line
                    continue;
                }
            }
        }

        fclose($handle);

        $this->info("\n✅ Successfully imported {$imported} records!");

        // Show summary
        $totalMonthly = ConversionData::sum('monthly_link_value_usd');
        $totalContract = ConversionData::sum('total_contract_value_usd');

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Records', $imported],
                ['Total Customers', DB::table('conversion_data')->distinct('customer_name')->count()],
                ['Total Monthly Value (USD)', '$' . number_format($totalMonthly, 2)],
                ['Total Contract Value (USD)', '$' . number_format($totalContract, 2)],
            ]
        );

        return 0;
    }

    /**
     * Fix encoding issues in a string
     */
    private function fixEncoding($string)
    {
        // Remove non-breaking spaces and other problematic characters
        $string = str_replace(["\xC2\xA0", "\xA0"], ' ', $string); // Non-breaking space

        // Convert to UTF-8 if needed
        if (!mb_check_encoding($string, 'UTF-8')) {
            $string = mb_convert_encoding($string, 'UTF-8', 'auto');
        }

        // Remove any remaining invalid UTF-8 characters
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');

        return $string;
    }

    /**
     * Parse a tab-separated line
     */
    private function parseLine($line)
    {
        // Custom CSV parsing to handle mixed formats
        $columns = [];
        $currentColumn = '';
        $inQuotes = false;

        for ($i = 0; $i < strlen($line); $i++) {
            $char = $line[$i];

            if ($char === '"') {
                $inQuotes = !$inQuotes;
            } elseif ($char === "\t" && !$inQuotes) {
                $columns[] = trim($currentColumn, '" ');
                $currentColumn = '';
            } else {
                $currentColumn .= $char;
            }
        }

        // Add the last column
        $columns[] = trim($currentColumn, '" ');

        // Ensure we have at least 15 columns
        while (count($columns) < 15) {
            $columns[] = '';
        }

        // Map columns to record
        return [
            'customer_ref' => $this->cleanString($columns[0]),
            'customer_id' => $this->cleanString($columns[1]),
            'customer_name' => $this->cleanString($columns[2]),
            'route_name' => $this->cleanString($columns[3]),
            'links_name' => $this->cleanString($columns[4]),
            'cores_leased' => $this->cleanNumeric($columns[5], 'int'),
            'bandwidth' => $this->cleanString($columns[6]),
            'distance_km' => $this->cleanNumeric($columns[7], 'float'),
            'price_per_core_per_km_per_month_usd' => $this->cleanNumeric($columns[8], 'float'),
            'monthly_link_value_usd' => $this->cleanNumeric($columns[9], 'float'),
            'monthly_link_kes' => $this->cleanNumeric($columns[10], 'float'),
            'link_class' => $this->cleanString($columns[11]),
            'contract_duration_yrs' => $this->cleanNumeric($columns[12], 'int'),
            'total_contract_value_usd' => $this->cleanNumeric($columns[13], 'float'),
            'total_contract_value_kes' => $this->cleanNumeric($columns[14], 'float'),
        ];
    }

    /**
     * Clean string values
     */
    private function cleanString($value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        $value = trim($value);

        // Remove non-ASCII characters except basic punctuation
        $value = preg_replace('/[^\x20-\x7E\xA0]/u', '', $value);

        // Remove any remaining quotes
        $value = trim($value, "\"' \t\n\r\0\x0B");

        return $value === '' ? null : $value;
    }

    /**
     * Clean numeric values
     */
    private function cleanNumeric($value, $type = 'float')
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        $value = trim($value);

        // Remove quotes, commas, and spaces
        $value = str_replace(['"', ',', ' ', '$'], '', $value);

        // Extract numeric part
        $numericValue = '';
        $hasDecimal = false;
        $hasNegative = false;

        for ($i = 0; $i < strlen($value); $i++) {
            $char = $value[$i];

            if ($char === '-' && $i === 0) {
                // Allow negative sign at the beginning
                $numericValue .= $char;
                $hasNegative = true;
            } elseif ($char === '.' && !$hasDecimal) {
                // Allow one decimal point
                $numericValue .= $char;
                $hasDecimal = true;
            } elseif (ctype_digit($char)) {
                $numericValue .= $char;
            }
        }

        if ($numericValue === '' || $numericValue === '-' || $numericValue === '.') {
            return null;
        }

        if ($type === 'int') {
            return (int) $numericValue;
        }

        return (float) $numericValue;
    }
}
