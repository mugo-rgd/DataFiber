<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // Exchange Rate Settings
            [
                'key' => 'usd_to_kes_rate',
                'value' => '130.04',
                'type' => 'decimal',
                'description' => 'Current USD to KES exchange rate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'kes_to_usd_rate',
                'value' => '0.00769',
                'type' => 'decimal',
                'description' => 'Current KES to USD exchange rate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'exchange_rate_source',
                'value' => 'manual',
                'type' => 'string',
                'description' => 'Exchange rate source (manual/api/central_bank)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'exchange_rate_last_updated',
                'value' => now()->toDateTimeString(),
                'type' => 'string',
                'description' => 'Last time exchange rate was updated',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Billing Settings
            [
                'key' => 'auto_billing_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable automatic billing generation',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'auto_billing_day_of_month',
                'value' => '1',
                'type' => 'integer',
                'description' => 'Day of month to generate automatic billings',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'billing_reminder_days_before',
                'value' => '7',
                'type' => 'integer',
                'description' => 'Days before due date to send reminders',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'overdue_reminder_days_after',
                'value' => '1,7,14,30',
                'type' => 'string',
                'description' => 'Days after due date to send overdue reminders (comma-separated)',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Tax Settings
            [
                'key' => 'default_vat_rate',
                'value' => '16',
                'type' => 'decimal',
                'description' => 'Default VAT rate percentage',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_withholding_tax_rate',
                'value' => '5',
                'type' => 'decimal',
                'description' => 'Default withholding tax rate percentage',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'enable_tax_calculation',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable automatic tax calculation on invoices',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Payment Settings
            [
                'key' => 'late_payment_interest_rate',
                'value' => '2',
                'type' => 'decimal',
                'description' => 'Late payment interest rate percentage per month',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'early_payment_discount',
                'value' => '2',
                'type' => 'decimal',
                'description' => 'Early payment discount percentage',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'early_payment_discount_days',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Days within which early payment discount applies',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Email Settings
            [
                'key' => 'email_notifications_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable email notifications for billing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'billing_email_recipients',
                'value' => 'billing@darkfibre-crm.com',
                'type' => 'string',
                'description' => 'Email addresses for billing notifications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'send_invoice_attachments',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Send PDF attachments with invoice emails',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Report Settings
            [
                'key' => 'default_report_format',
                'value' => 'pdf',
                'type' => 'string',
                'description' => 'Default report export format (pdf/excel/csv)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'report_retention_days',
                'value' => '90',
                'type' => 'integer',
                'description' => 'Number of days to keep generated reports',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // System Settings
            [
                'key' => 'company_name',
                'value' => 'DarkFibre CRM',
                'type' => 'string',
                'description' => 'Company name displayed on invoices',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_email',
                'value' => 'info@darkfibre-crm.com',
                'type' => 'string',
                'description' => 'Company email address',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_phone',
                'value' => '+254 XXX XXX XXX',
                'type' => 'string',
                'description' => 'Company phone number',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_address',
                'value' => 'Nairobi, Kenya',
                'type' => 'string',
                'description' => 'Company physical address',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'kra_pin',
                'value' => 'P051234567X',
                'type' => 'string',
                'description' => 'KRA PIN for tax purposes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'vat_registration_number',
                'value' => 'VAT-123456789',
                'type' => 'string',
                'description' => 'VAT registration number',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settings as $setting) {
            // Check if setting exists
            $exists = DB::table('settings')
                ->where('key', $setting['key'])
                ->exists();

            if (!$exists) {
                DB::table('settings')->insert($setting);
            } else {
                // Update existing settings
                DB::table('settings')
                    ->where('key', $setting['key'])
                    ->update([
                        'value' => $setting['value'],
                        'type' => $setting['type'],
                        'description' => $setting['description'],
                        'updated_at' => now(),
                    ]);
            }
        }

        $this->command->info('Settings seeded successfully!');
    }
}
