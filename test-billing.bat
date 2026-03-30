@echo off
echo Starting billing commands...
cd /d G:\project

echo Running billing stats...
php artisan billing:stats

echo Generating invoices...
php artisan billing:generate

echo Done!
pause