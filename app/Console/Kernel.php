<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Zoom account rotation disabled — not needed on production.
        // $schedule->call('App\Http\Controllers\ScheduleJobController@check_and_update_zoom_account_minutes')->dailyAt('00:00')->emailOutputOnFailure('nouman.hmnt@gmail.com');
        // $schedule->call('App\Http\Controllers\ScheduleJobController@check_and_activate_zoom_account')->everyTwoMinutes()->emailOutputOnFailure('nouman.hmnt@gmail.com');
         
        // $schedule->call('App\Http\Controllers\ScheduleJobController@processTrialEnrollments')->monthlyOn(1, '00:00')->timezone('Asia/Karachi');


     


        // run daily at midnight Karachi time
        // $schedule->call('App\Http\Controllers\ScheduleJobController@processTrialEnrollments')
        //     ->dailyAt('00:00')
        //     ->timezone('Asia/Karachi');


        // $schedule->command('process-trial-enrollments')
        //     ->dailyAt('00:00')
        //     ->timezone('Asia/Karachi');
         

        // $schedule->call('App\Http\Controllers\ScheduleJobController@sendReminderEmailEnrollments')->monthlyOn(1, '00:00')->timezone('Asia/Karachi');
        // $schedule->call('App\Http\Controllers\ScheduleJobController@sendReminderEmailEnrollments')
        //     ->dailyAt('00:00')
        //     ->timezone('Asia/Karachi')
        //     ->when(function () {
        //         // Run only on even-numbered days
        //         return now()->day % 2 === 0;
        //     });
        

        // Run only on even-numbered days at 00:00 Asia/Karachi
        // $schedule->command('emails:queue-pending-enrollments')
        //     ->dailyAt('00:00')
        //     ->timezone('Asia/Karachi')
        //     ->when(function () {
        //         return now()->day % 2 === 0;
        //     });


        $schedule->command('process-trial-enrollments')
            ->monthlyOn(1, '00:00')
            ->timezone('Asia/Karachi');


        $schedule->command('emails:queue-pending-enrollments')
            ->dailyAt('00:00')
            ->timezone('Asia/Karachi')
            ->when(function () {
                $day = now()->day;

                // 1. Logic for 5th to 20th: Every 5 days (5, 10, 15, 20)
                if ($day >= 5 && $day <= 20) {
                    return ($day - 5) % 5 === 0;
                }

                // 2. Logic for 21st to 26th: Every 3 days (23, 26)
                if ($day > 20 && $day <= 26) {
                    return ($day - 20) % 3 === 0;
                }

                // 3. Logic for 27th onwards: Every 2 days (28, 30, etc.)
                if ($day > 26) {
                    return ($day - 26) % 2 === 0;
                }

                return false;
            });
            


        // Run only on odd-numbered days at 5:40 PM Asia/Karachi
        // $schedule->command('emails:queue-pending-enrollments')
        //     ->dailyAt('00:00')
        //     ->timezone('Asia/Karachi')
        //     ->when(function () {
        //         return now()->day % 2 !== 0; // odd day
        //     });

        // $schedule->call('App\Http\Controllers\ScheduleJobController@check_and_update_class_statuses')->everyTwoMinutes()->emailOutputOnFailure('nouman.hmnt@gmail.com');
         $schedule->call('App\Http\Controllers\ScheduleJobController@check_and_update_class_statuses')->everyTwoMinutes()->emailOutputOnFailure('nouman.hmnt@gmail.com');

        // Disabled: access-token expiry is ~1h; this job cleared valid refresh-token sessions.
        // $schedule->command('google:clear-expired-instructor-tokens')
        //     ->dailyAt('00:15')
        //     ->timezone('Asia/Karachi');

        // Meezan-only recurring donations: charge due schedules daily (recurringPayment.do)
        $schedule->command('donations:process-recurring-schedules')
            ->dailyAt('00:05')
            ->timezone('Asia/Karachi');

        // Meezan reconcile: poll getOrderStatusExtended for unpaid LMS/donation orders (missed redirects)
        $schedule->command('meezan:reconcile-pending-orders')
            ->everyMinute()
            ->withoutOverlapping()
            ->timezone('Asia/Karachi');

        $schedule->command('vault:build-stale-manifests')->everyTenMinutes();
        $schedule->command('vault:ensure-youtube-downloads')->hourly();

        $schedule->command('vault:expire-licenses')
            ->dailyAt('00:10')
            ->timezone('Asia/Karachi');
      
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
