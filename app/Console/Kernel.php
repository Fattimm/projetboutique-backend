<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // // Envoyer le résumé des dettes dues tous les vendredis à 14h
        // $schedule->job(new \App\Jobs\ProcessDettes)->weeklyOn(5, '14:00');

        // // Archiver les dettes soldées chaque fin de journée
        // $schedule->job(new \App\Jobs\ProcessDettes)->dailyAt('23:59');
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
