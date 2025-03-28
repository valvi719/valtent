<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\CalculateInterest;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

app()->resolving(Schedule::class, function (Schedule $schedule) {
    $schedule->command('interest:calculate')->yearly();
});

