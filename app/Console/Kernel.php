<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\ProcessLeaseBilling::class,
        \App\Console\Commands\ImportCompanyUsers::class,
        \App\Console\Commands\ProcessNewLeases::class, // Add this
        \App\Console\Commands\ImportCompanyProfiles::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
         $schedule->command('leases:process-new --hours=48')
             ->everyThirtyMinutes()
             ->withoutOverlapping(15)
             ->appendOutputTo(storage_path('logs/new-leases.log'));
             
        // Process lease billing every hour (to catch new leases quickly)
        $schedule->command('leases:process-billing')
                 ->hourly()
                 ->withoutOverlapping(30)
                 ->appendOutputTo(storage_path('logs/lease-billing-hourly.log'))
                 ->onSuccess(function () {
                     Log::info('Hourly lease billing processed successfully');
                 })
                 ->onFailure(function () {
                     Log::error('Hourly lease billing failed');
                 });

        // Also run a full daily billing at 2 AM
        $schedule->command('leases:process-billing --force')
                 ->dailyAt('02:00')
                 ->withoutOverlapping(60)
                 ->appendOutputTo(storage_path('logs/lease-billing-daily.log'))
                 ->onSuccess(function () {
                     Log::info('Daily full lease billing processed successfully');
                 })
                 ->onFailure(function () {
                     Log::error('Daily full lease billing failed');
                 });

        // Your existing billing commands
        $schedule->command('billing:process-daily')->dailyAt('03:00');
        $schedule->command('billing:generate')->dailyAt('04:00');
        $schedule->command('tevin:monitor')->everyThirtyMinutes();

        $schedule->command('queue:prune-failed')->daily();
        $schedule->command('queue:flush')->weekly();
        $schedule->command('exchange:sync')->dailyAt('00:00');
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     */
    protected function scheduleTimezone(): string
    {
        return 'Africa/Nairobi'; // Set to Kenya timezone
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
