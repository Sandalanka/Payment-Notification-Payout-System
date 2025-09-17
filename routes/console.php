<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

//Schedule::command('payouts:send')->dailyAt('00:10')->timezone('Asia/Colombo');
Schedule::command('payouts:send')->everyMinute();