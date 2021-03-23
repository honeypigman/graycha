<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    // ->cron('* * * * *');	지정한 Cron 형태로 작업 실행
    // ->everyMinute();	매분 마다 작업 실행
    // ->everyFiveMinutes();	5분 간격으로 작업 실행
    // ->everyTenMinutes();	10분 간격으로 작업 실행
    // ->everyFifteenMinutes();	15분 간격으로 작업 실행
    // ->everyThirtyMinutes();	30분 간격으로 작업 실행
    // ->hourly();	1시간 간격으로 작업 실행
    // ->hourlyAt(17);	매시간 17분에 실행
    // ->daily();	한밤중을 기준으로 하루에 한번 작업 실행
    // ->dailyAt('13:00');	매일 13:00에 작업 실행
    // ->twiceDaily(1, 13);	하루중 1:00 & 13:00 에 작업 실행(총2번)
    // ->weekly();	매주 일요일 00:00 에 작업 실행
    // ->weeklyOn(1, '8:00');	매주 월요일 8시에 작업 실행
    // ->monthly();	매달 1일 00:00 에 작업 실행
    // ->monthlyOn(4, '15:00');	매달 4일 15:00분에 작업 실행
    // ->quarterly();	분기별 첫번째 날 00:00 에 작업 실행
    // ->yearly();	매년 1월1일 00:00 에 작업 실행
    // ->timezone('America/New_York');	타임존 지정

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\KairspecMsrstnAll::class,
        \App\Console\Commands\KairspecStationInfoAll::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // KairSpec 전체 측정소 데이터 획득
        $schedule->command('kairspec:getMsrstnInfoAll')->daily();
        // KairSpec 시도별 측정소 정보 획득
        $schedule->command('kairspec:getStationInfoAll')->hourlyAt(15);
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
