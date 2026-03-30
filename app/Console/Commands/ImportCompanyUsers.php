<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ImportCompanyUsers extends Command
{
    protected $signature = 'import:company-users {file}';
    protected $description = 'Import company users from CSV (name as company name)';

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return;
        }

        $handle = fopen($file, 'r');
        // Use default comma delimiter
        $header = fgetcsv($handle);

        $this->info("Starting import...");

        $count = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 7) {
                continue; // Skip invalid rows
            }

            $data = array_combine($header, $row);

            // Clean the data
            $companyName = trim($data['name']);
            $email = $this->cleanEmail(trim($data['email']));
            $phone = trim($data['phone']);

            // Skip if company name is empty
            if (empty($companyName)) {
                $errors[] = "Skipping row with empty company name: " . implode(', ', $row);
                continue;
            }

            // Generate email if empty
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = $this->generateEmailFromCompany($companyName);
            }

            // Check if user already exists
            $existing = User::where('email', $email)->orWhere('name', $companyName)->first();

            if ($existing) {
                $errors[] = "User already exists: {$companyName} ({$email})";
                continue;
            }

            try {
                User::create([
                    'name' => $companyName,
                    'company_name' => $companyName,
                    'email' => $email,
                    'phone' => $this->cleanPhone($phone),
                    'password' => Hash::make($data['password']),
                    'role' => $data['role'] ?? 'customer',
                    'status' => $this->mapStatus($data['status'] ?? 'active'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $count++;
                $this->info("Imported: {$companyName}");

            } catch (\Exception $e) {
                $errors[] = "Error importing {$companyName}: " . $e->getMessage();
                Log::error('Import error: ' . $e->getMessage());
            }
        }

        fclose($handle);

        $this->newLine();
        $this->info("✅ Successfully imported {$count} company users.");

        if (!empty($errors)) {
            $this->error("Encountered " . count($errors) . " errors:");
            foreach ($errors as $error) {
                $this->warn("  • " . $error);
            }
        }
    }

    private function cleanEmail($email)
    {
        // Fix common email issues
        $email = str_replace('<>', '-', $email);
        $email = str_replace(' ', '', $email);

        // Handle dual emails (take first one)
        if (strpos($email, '.') !== false && substr_count($email, '@') > 1) {
            $parts = explode('.', $email);
            $email = '';
            foreach ($parts as $part) {
                if (strpos($part, '@') !== false) {
                    $email .= $part . '.';
                    break;
                }
            }
            $email = rtrim($email, '.');
        }

        return $email;
    }

    private function generateEmailFromCompany($companyName)
    {
        $cleanName = preg_replace('/[^a-zA-Z0-9]/', '', $companyName);
        $cleanName = strtolower(substr($cleanName, 0, 20));

        $email = $cleanName . '@company.placeholder';

        // Ensure uniqueness
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $cleanName . $counter . '@company.placeholder';
            $counter++;
        }

        return $email;
    }

    private function cleanPhone($phone)
    {
        $phone = trim($phone);

        // If phone is just "+254" or incomplete, return null
        if (empty($phone) || $phone === '+254' || strlen($phone) < 10) {
            return null;
        }

        return $phone;
    }

    private function mapStatus($status)
    {
        if ($status === '1' || $status === 1 || strtolower($status) === 'active') {
            return 'active';
        }

        return 'inactive';
    }
}
