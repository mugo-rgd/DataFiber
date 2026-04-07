<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class ManageSettings extends Command
{
    protected $signature = 'settings:manage {action} {key?} {value?}';
    protected $description = 'Manage application settings';

    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'get':
                $key = $this->argument('key');
                $value = Setting::get($key);
                $this->info("{$key}: " . ($value ?? 'null'));
                break;

            case 'set':
                $key = $this->argument('key');
                $value = $this->argument('value');
                Setting::set($key, $value);
                $this->info("Setting {$key} updated to {$value}");
                break;

            case 'list':
                $settings = Setting::all();
                $this->table(['Key', 'Value', 'Type', 'Updated At'],
                    $settings->map(fn($s) => [$s->key, $s->value, $s->type, $s->updated_at])
                );
                break;

            case 'update-exchange-rate':
                $rate = $this->ask('Enter new USD to KES exchange rate', 130);
                Setting::set('usd_to_kes_rate', (float) $rate, 'decimal');
                $this->info("Exchange rate updated to {$rate} KES per USD");
                break;

            default:
                $this->error("Unknown action: {$action}");
                $this->info("Available actions: get, set, list, update-exchange-rate");
        }
    }
}
