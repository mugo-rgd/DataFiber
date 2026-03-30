@echo off
cd /d G:\project\darkfibre-crm
php artisan schedule:run
echo Laravel scheduler ran at %time% on %date% >> G:\project\darkfibre-crm\storage\logs\scheduler.log