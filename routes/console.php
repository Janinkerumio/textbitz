<?php

use App\Services\Remote\ServerConnectivityService;
use App\Services\Data\PullDataFromServer;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(fn () => ServerConnectivityService::checkAndNotify())
    ->everyMinute()
    ->name('server-connectivity-heartbeat')
    ->withoutOverlapping(1);

Schedule::call(fn () => PullDataFromServer::reconcileInFlightBlasts())
    ->everyMinute()
    ->name('reconcile-inflight-blasts')
    ->withoutOverlapping(1);
