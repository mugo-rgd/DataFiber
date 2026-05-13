<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop existing triggers if they exist
        DB::unprepared('DROP TRIGGER IF EXISTS after_financial_parameters_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS after_financial_parameters_update');
        DB::unprepared('DROP TRIGGER IF EXISTS after_financial_parameters_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS after_settings_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS after_settings_update');

        // Trigger for financial_parameters INSERT
        DB::unprepared('
            CREATE TRIGGER after_financial_parameters_insert
            AFTER INSERT ON financial_parameters
            FOR EACH ROW
            BEGIN
                IF NEW.parameter_name = \'kes_to_usd\' THEN
                    INSERT INTO settings (`key`, `value`, `type`, `description`, `created_at`, `updated_at`)
                    VALUES (\'kes_to_usd_rate\', CAST(NEW.parameter_value AS CHAR), \'decimal\', NEW.description, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE
                        `value` = CAST(NEW.parameter_value AS CHAR),
                        `description` = NEW.description,
                        `updated_at` = NOW();

                    INSERT INTO settings (`key`, `value`, `type`, `description`, `created_at`, `updated_at`)
                    VALUES (\'usd_to_kes_rate\', CAST(1/NEW.parameter_value AS CHAR), \'decimal\', CONCAT(\'USD to KES rate (based on KES to USD rate)\'), NOW(), NOW())
                    ON DUPLICATE KEY UPDATE
                        `value` = CAST(1/NEW.parameter_value AS CHAR),
                        `updated_at` = NOW();
                END IF;

                IF NEW.parameter_name = \'vat_rate\' THEN
                    INSERT INTO settings (`key`, `value`, `type`, `description`, `created_at`, `updated_at`)
                    VALUES (\'default_vat_rate\', CAST(NEW.parameter_value * 100 AS CHAR), \'decimal\', NEW.description, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE
                        `value` = CAST(NEW.parameter_value * 100 AS CHAR),
                        `description` = NEW.description,
                        `updated_at` = NOW();
                END IF;

                IF NEW.parameter_name = \'late_payment_interest_rate\' THEN
                    INSERT INTO settings (`key`, `value`, `type`, `description`, `created_at`, `updated_at`)
                    VALUES (\'late_payment_interest_rate\', CAST(NEW.parameter_value * 100 AS CHAR), \'decimal\', NEW.description, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE
                        `value` = CAST(NEW.parameter_value * 100 AS CHAR),
                        `description` = NEW.description,
                        `updated_at` = NOW();
                END IF;
            END
        ');

        // Trigger for financial_parameters UPDATE
        DB::unprepared('
            CREATE TRIGGER after_financial_parameters_update
            AFTER UPDATE ON financial_parameters
            FOR EACH ROW
            BEGIN
                IF NEW.parameter_name = \'kes_to_usd\' AND OLD.parameter_value != NEW.parameter_value THEN
                    UPDATE settings
                    SET `value` = CAST(NEW.parameter_value AS CHAR),
                        `updated_at` = NOW()
                    WHERE `key` = \'kes_to_usd_rate\';

                    UPDATE settings
                    SET `value` = CAST(1/NEW.parameter_value AS CHAR),
                        `updated_at` = NOW()
                    WHERE `key` = \'usd_to_kes_rate\';
                END IF;

                IF NEW.parameter_name = \'vat_rate\' AND OLD.parameter_value != NEW.parameter_value THEN
                    UPDATE settings
                    SET `value` = CAST(NEW.parameter_value * 100 AS CHAR),
                        `description` = NEW.description,
                        `updated_at` = NOW()
                    WHERE `key` = \'default_vat_rate\';
                END IF;

                IF NEW.parameter_name = \'late_payment_interest_rate\' AND OLD.parameter_value != NEW.parameter_value THEN
                    UPDATE settings
                    SET `value` = CAST(NEW.parameter_value * 100 AS CHAR),
                        `description` = NEW.description,
                        `updated_at` = NOW()
                    WHERE `key` = \'late_payment_interest_rate\';
                END IF;
            END
        ');

        // Trigger for settings INSERT
        DB::unprepared('
            CREATE TRIGGER after_settings_insert
            AFTER INSERT ON settings
            FOR EACH ROW
            BEGIN
                IF NEW.`key` = \'usd_to_kes_rate\' THEN
                    IF NOT EXISTS (SELECT 1 FROM financial_parameters WHERE parameter_name = \'kes_to_usd\' AND effective_from <= NOW() AND (effective_to IS NULL OR effective_to >= NOW())) THEN
                        INSERT INTO financial_parameters (parameter_name, parameter_value, effective_from, effective_to, currency_code, country_code, description, created_by, created_at, updated_at)
                        VALUES (\'kes_to_usd\', 1/CAST(NEW.`value` AS DECIMAL(10,6)), CURDATE(), NULL, \'KES\', \'KEN\', CONCAT(\'Exchange rate synced from settings on \', NOW()), 1, NOW(), NOW());
                    END IF;
                END IF;

                IF NEW.`key` = \'default_vat_rate\' THEN
                    IF NOT EXISTS (SELECT 1 FROM financial_parameters WHERE parameter_name = \'vat_rate\' AND effective_from <= NOW() AND (effective_to IS NULL OR effective_to >= NOW())) THEN
                        INSERT INTO financial_parameters (parameter_name, parameter_value, effective_from, effective_to, currency_code, country_code, description, created_by, created_at, updated_at)
                        VALUES (\'vat_rate\', CAST(NEW.`value` AS DECIMAL(10,6))/100, CURDATE(), NULL, NULL, \'KEN\', CONCAT(\'VAT rate synced from settings on \', NOW()), 1, NOW(), NOW());
                    END IF;
                END IF;

                IF NEW.`key` = \'late_payment_interest_rate\' THEN
                    IF NOT EXISTS (SELECT 1 FROM financial_parameters WHERE parameter_name = \'late_payment_interest_rate\' AND effective_from <= NOW() AND (effective_to IS NULL OR effective_to >= NOW())) THEN
                        INSERT INTO financial_parameters (parameter_name, parameter_value, effective_from, effective_to, currency_code, country_code, description, created_by, created_at, updated_at)
                        VALUES (\'late_payment_interest_rate\', CAST(NEW.`value` AS DECIMAL(10,6))/100, CURDATE(), NULL, NULL, \'KEN\', CONCAT(\'Late payment rate synced from settings on \', NOW()), 1, NOW(), NOW());
                    END IF;
                END IF;
            END
        ');

        // Trigger for settings UPDATE
        DB::unprepared('
            CREATE TRIGGER after_settings_update
            AFTER UPDATE ON settings
            FOR EACH ROW
            BEGIN
                IF NEW.`key` = \'usd_to_kes_rate\' AND OLD.`value` != NEW.`value` THEN
                    UPDATE financial_parameters
                    SET parameter_value = 1/CAST(NEW.`value` AS DECIMAL(10,6)),
                        updated_at = NOW()
                    WHERE parameter_name = \'kes_to_usd\'
                      AND effective_from <= NOW()
                      AND (effective_to IS NULL OR effective_to >= NOW());
                END IF;

                IF NEW.`key` = \'default_vat_rate\' AND OLD.`value` != NEW.`value` THEN
                    UPDATE financial_parameters
                    SET parameter_value = CAST(NEW.`value` AS DECIMAL(10,6))/100,
                        updated_at = NOW()
                    WHERE parameter_name = \'vat_rate\'
                      AND effective_from <= NOW()
                      AND (effective_to IS NULL OR effective_to >= NOW());
                END IF;

                IF NEW.`key` = \'late_payment_interest_rate\' AND OLD.`value` != NEW.`value` THEN
                    UPDATE financial_parameters
                    SET parameter_value = CAST(NEW.`value` AS DECIMAL(10,6))/100,
                        updated_at = NOW()
                    WHERE parameter_name = \'late_payment_interest_rate\'
                      AND effective_from <= NOW()
                      AND (effective_to IS NULL OR effective_to >= NOW());
                END IF;
            END
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS after_financial_parameters_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS after_financial_parameters_update');
        DB::unprepared('DROP TRIGGER IF EXISTS after_financial_parameters_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS after_settings_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS after_settings_update');
    }
};
