<?php

namespace App\Listeners;

use App\Events\ServerConnectionRestored;
use App\Services\DataSyncToJob;
use App\Services\RemoteAuthService;

class SyncPendingClientData
{
    public function handle(ServerConnectionRestored $event): void
    {
        // Fired once, right when the remote server transitions from
        // unreachable -> reachable. Wire up pending client-server sync
        // tasks here (remote auth reconnection, blast/recipient syncs, etc).

        RemoteAuthService::flushPendingAuth();
        RemoteAuthService::verifyAllTokens();
        DataSyncToJob::retryPendingSyncs();
    }
}
