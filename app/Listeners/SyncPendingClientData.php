<?php

namespace App\Listeners;

use App\Events\ServerConnectionRestored;
use App\Services\Data\DataSyncToJob;
use App\Services\Remote\RemoteAuthService;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncPendingClientData
{
    public function handle(ServerConnectionRestored $event): void
    {
        $this->safely('flushPendingAuth', fn () => RemoteAuthService::flushPendingAuth());
        $this->safely('verifyAllTokens', fn () => RemoteAuthService::verifyAllTokens());
        $this->safely('retryPendingSyncs', fn () => DataSyncToJob::retryPendingSyncs());
    }

    protected function safely(string $step, \Closure $task): void
    {
        try {
            $task();
        } catch (Throwable $e) {
            Log::error("SyncPendingClientData: [{$step}] threw, continuing", [
                'class' => get_class($e),
                'message' => $e->getMessage(),
            ]);
        }
    }
}
