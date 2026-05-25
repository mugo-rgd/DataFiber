<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('reports:generate-executive')
    ->dailyAt('23:55')
    ->withoutOverlapping();

Schedule::command('reports:email-executive')
    ->dailyAt('07:30')
    ->withoutOverlapping();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
