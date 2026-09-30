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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 1) Nonaktifkan mahasiswa yang masa aktifnya sudah lewat
        $schedule->command('mahasiswa:update-status')->dailyAt('00:05');

        // 2) Rolling ruangan harian: pindahkan mahasiswa ke ruangan sesuai jadwal
        //    hari ini, recalculate kuota, dan keluarkan dari ruangan bila periode habis.
        //    (sebelumnya command ini tidak pernah dijadwalkan sehingga data ruangan basi)
        $schedule->command('room:sync')->dailyAt('00:10');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
