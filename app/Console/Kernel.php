<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Modules\Orders\Console\Commands\CashFlow;
use App\Modules\Orders\Console\Commands\ReportsByDistricts;
use App\Modules\Operators\Console\Commands\ContactsExpired;
use App\Modules\Systems\Console\Commands\CallHistory;
use App\Modules\Systems\Console\Commands\Notification;
use App\Modules\Orders\Console\Commands\NotifyOverTime;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CashFlow::class,
        ReportsByDistricts::class,
        ContactsExpired::class,
        CallHistory::class,
        NotifyOverTime::class,
        Notification::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('orders:cash-flow');
        $schedule->command('orders:report-by-districts');
        $schedule->command('contacts:expired');
        $schedule->command('systems:call-history');
        $schedule->command('orders:overtime-status')->daily();
        $schedule->command('systems:send-notification');
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
