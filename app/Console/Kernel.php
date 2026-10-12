<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Register the cleanup command
        \App\Console\Commands\CleanExpiredMarketNews::class,
        \App\Console\Commands\ProcessMonthlyEarnings::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Run daily at 02:00 server time to remove expired market news
        $schedule->command('market-insights:clean-expired')->dailyAt('02:00');
        // Process monthly earnings once per day (will insert due payments)
        $schedule->command('earnings:process-monthly')->dailyAt('03:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
