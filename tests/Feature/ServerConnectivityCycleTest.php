<?php

use App\Events\ServerConnectionRestored;
use App\Services\Remote\ServerConnectivityService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

it('fires restored event on every online-offline-online cycle', function () {
    Event::fake([ServerConnectionRestored::class]);
    Cache::flush();

    // Single Http::fake() call driving a scripted sequence — calling
    // Http::fake() again mid-test does NOT overwrite an existing '*'
    // stub in Laravel (laravel/framework#49398), so re-faking between
    // checkAndNotify() calls silently no-ops after the first call.
    $script = [
        true,  // 1: online   (false -> true : fires, first-run)
        false, // 2: offline  (true  -> false : no event)
        true,  // 3: online   (false -> true : fires)
        false, // 4: offline  (true  -> false : no event)
        true,  // 5: online   (false -> true : fires)
    ];
    $call = 0;

    Http::fake(function () use (&$script, &$call) {
        $online = $script[$call] ?? true;
        $call++;

        if (!$online) {
            throw new ConnectionException('down');
        }

        return Http::response('', 200);
    });

    foreach ($script as $_) {
        ServerConnectivityService::checkAndNotify();
    }

    Event::assertDispatchedTimes(ServerConnectionRestored::class, 3);
});