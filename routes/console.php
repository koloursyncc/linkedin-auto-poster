<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Post at 9:00 AM IST (03:30 UTC)
Schedule::command('linkedin:post')
    ->dailyAt('03:30')
    ->timezone('UTC')
    ->withoutOverlapping()
    ->runInBackground();

// Post at 6:00 PM IST (12:30 UTC)
Schedule::command('linkedin:post')
    ->dailyAt('12:30')
    ->timezone('UTC')
    ->withoutOverlapping()
    ->runInBackground();

// Poll comments every 15 minutes
Schedule::command('linkedin:poll')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground();