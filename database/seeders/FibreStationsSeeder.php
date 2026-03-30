<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class FibreStationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Path to your CSV file
        $csvFile = database_path('seeders\data\fibre_stations.csv');

        if (!File::exists($csvFile)) {
            $this->command->error("CSV file not found: {$csvFile}");
            return;
        }

        $csvData = array_map('str_getcsv', file($csvFile));

        // Remove header row
        array_shift($csvData);

        $stations = [];
        foreach ($csvData as $row) {
            // Skip empty rows
            if (count($row) < 10) {
                continue;
            }

            $stations[] = [
                'lat' => $this->parseFloat($row[0]),
                'lng' => $this->parseFloat($row[1]),
                'name' => $row[2] ?? '',
                'capacity' => $this->parseCapacity($row[3]),
                'fibreStatus' => $row[4] ?? 'Available',
                'darkFibreCores' => intval($row[5]) ?? 12,
                'connectionType' => $row[6] ?? 'Patch Panel',
                'owner' => $row[7] ?? '',
                'area' => $row[8] ?? '',
                'location' => $row[9] ?? '',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert in chunks
        foreach (array_chunk($stations, 100) as $chunk) {
            DB::table('fibre_stations')->insert($chunk);
        }

        $this->command->info('Successfully seeded ' . count($stations) . ' fibre stations.');
    }

    private function parseFloat($value)
    {
        if ($value === null || $value === '') {
            return 0.0;
        }
        return floatval($value);
    }

    private function parseCapacity($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Handle comma-separated values like "11,33"
        if (strpos($value, ',') !== false) {
            $values = explode(',', $value);
            return floatval(trim($values[0]));
        }

        return floatval($value);
    }
}
