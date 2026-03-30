<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CompanyProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ImportCompanyProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:company-profiles {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import company profiles from Excel/CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return Command::FAILURE;
        }

        $this->info('Starting import...');

        try {
            // For CSV files
            if (Str::endsWith($filePath, '.csv')) {
                $this->importFromCSV($filePath);
            }
            // For Excel files (requires maatwebsite/excel package)
            elseif (Str::endsWith($filePath, ['.xlsx', '.xls'])) {
                $this->importFromExcel($filePath);
            }
            else {
                $this->error('Unsupported file format. Please use .csv, .xlsx, or .xls');
                return Command::FAILURE;
            }

            $this->info("\n✅ Successfully imported company profiles.");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            Log::error('Company profiles import failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Import data from CSV file
     */
    private function importFromCSV($filePath)
    {
        $file = fopen($filePath, 'r');
        $header = fgetcsv($file); // Get header row

        $count = 0;

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);
            $this->createCompanyProfile($data);
            $count++;

            // Display progress
            $this->info("Imported: " . ($data['company_name'] ?? 'Unknown'));
        }

        fclose($file);
        $this->info("\nImported {$count} records from CSV.");
    }

    /**
     * Import data from Excel file (requires maatwebsite/excel package)
     */
    private function importFromExcel($filePath)
    {
        // Check if Excel package is installed
        if (!class_exists('Maatwebsite\Excel\Facades\Excel')) {
            $this->error('Please install maatwebsite/excel package: composer require maatwebsite/excel');
            return;
        }

        $data = Excel::toArray([], $filePath);

        if (empty($data) || empty($data[0])) {
            $this->error('No data found in Excel file.');
            return;
        }

        $rows = $data[0];
        $header = array_shift($rows); // Remove header row

        $count = 0;
        foreach ($rows as $row) {
            $data = array_combine($header, $row);
            $this->createCompanyProfile($data);
            $count++;

            // Display progress
            $this->info("Imported: " . ($data['company_name'] ?? 'Unknown'));
        }

        $this->info("\nImported {$count} records from Excel.");
    }

    /**
     * Create a company profile from data array
     */
    private function createCompanyProfile($data)
    {
        // Map Excel columns to database columns
        $profileData = [
            'company_name' => $data['company_name'] ?? null,
            'user_id' => $data['user id'] ?? $data['user_id'] ?? 1, // Default to 1 if not provided
            'company_type' => strtolower($data['company type'] ?? 'private'),
            'sap_account' => $this->cleanSapAccount($data['sap account'] ?? null),
            'kra_pin' => $data['KRA PIN'] ?? $data['kra_pin'] ?? null,
            'phone_number' => $this->cleanPhoneNumber($data['phone number'] ?? $data['phone_number'] ?? null),
            'contact_phone_1' => $this->cleanPhoneNumber($data['contact phone 1'] ?? $data['contact_phone_1'] ?? null),
            'contact_name_1' => $data['contact name 1'] ?? $data['contact_name_1'] ?? null,
            'description' => $data['description'] ?? 'Dark fibre ISP',

            // Set default values for missing fields
            'registration_number' => '',
            'contact_name_2' => '',
            'contact_phone_2' => null,
            'physical_location' => '',
            'road' => '',
            'town' => '',
            'address' => '',
            'code' => '',
            'profile_photo' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Clean up company type to match ENUM values
        $profileData['company_type'] = $this->cleanCompanyType($profileData['company_type']);

        // Create the company profile
        CompanyProfile::create($profileData);
    }

    /**
     * Clean SAP account number (max 6 chars)
     */
    private function cleanSapAccount($sapAccount)
    {
        if (empty($sapAccount)) {
            return null;
        }

        // Trim and take first 6 characters
        $cleaned = substr(trim($sapAccount), 0, 6);

        // Check if it's a valid number, otherwise return null
        return is_numeric($cleaned) ? $cleaned : null;
    }

    /**
     * Clean phone number
     */
    private function cleanPhoneNumber($phone)
    {
        if (empty($phone) || $phone === '+254') {
            return null;
        }

        // Remove any non-digit except +
        $cleaned = preg_replace('/[^\d+]/', '', $phone);

        return $cleaned ?: null;
    }

    /**
     * Clean company type to match ENUM
     */
    private function cleanCompanyType($type)
    {
        $type = strtolower(trim($type));

        $validTypes = ['public', 'parastatal', 'county government', 'private', 'ngo'];

        // Check if type is valid
        if (in_array($type, $validTypes)) {
            return $type;
        }

        // Try to map common variations
        $mappings = [
            'gov' => 'public',
            'government' => 'public',
            'state' => 'public',
            'county' => 'county government',
            'non-governmental organization' => 'ngo',
            'non government organization' => 'ngo',
            'nonprofit' => 'ngo',
        ];

        return $mappings[$type] ?? 'private';
    }
}
