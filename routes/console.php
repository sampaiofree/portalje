<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('auth:clear-resets')->everyFifteenMinutes();

// The command owns the shared Redis lock, including for manual executions.
Schedule::command('ranking:xp:rebuild')->dailyAt('03:00')->timezone('America/Sao_Paulo')->runInBackground();
