<?php

use App\Services\Remote\ServerConnectivityService;
use App\Services\Data\PullDataFromServer;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function (){ 
        try {
            ServerConnectivityService::checkAndNotify();
        } catch (\Throwable $e) {
            Log::error('Heartbeat check threw', [
                'message' => $e->getMessage(),
                'class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    })
    ->everyMinute()
    ->name('server-connectivity-heartbeat')
    ->withoutOverlapping(1);

Schedule::call(fn () => PullDataFromServer::reconcileInFlightBlasts())
    ->everyMinute()
    ->name('reconcile-inflight-blasts')
    ->withoutOverlapping(1);
