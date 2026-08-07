<?php

namespace App\Jobs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Recipients;
use App\Services\RemoteApiClient;
use App\Services\ServerConnectivityService;

class PushBlastRecipientsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 0;
    public array $backoff = [10, 30, 60, 120, 300];

    public function retryUntil()
    {
        return now()->addDays(3);
    }

    public function __construct(
        public Model $blast,
        public array $recipients,
    ) {}

    public function middleware(): array
    {
        return [new RateLimited('recipient-pushes')];
    }

    public function handle(): void
    {
        if (!ServerConnectivityService::isOnline()) {
            Log::info('Server offline, delaying blast push', ['blast_id' => $this->blast->id]);
            $this->release(30);
            return;
        }

        if (!$this->blast->remote_id) 
        {
            Log::error('Attempted to push recipients before blast synced', ['blast_id' => $this->blast->id]);
            $this->fail();
            return;
        }

        $response = RemoteApiClient::post(
            $this->blast->user,
            "/api/blasts/{$this->blast->remote_id}/recipients",
            ['recipients' => $this->recipients]
        );

        $contactIds = array_column($this->recipients, 'local_contact_id');

        if ($response['result'] === RemoteApiClient::RESULT_SUCCESS) {
            Recipients::where('history_id', $this->blast->id)
                ->whereIn('contact_id', $contactIds)
                ->update(['sync_status' => 'synced']);
            return;
        }

        if ($response['result'] === RemoteApiClient::RESULT_UNAUTHORIZED) {
            Log::warning('Remote auth unavailable for recipient push', ['blast_id' => $this->blast->id]);

            if ($this->blast->user->remote_token) {
                $this->blast->user->update(['remote_token' => null]);
            }

            $this->release($this->backoff[$this->attempts() - 1] ?? end($this->backoff));
            return;
        }

        if ($response['result'] === RemoteApiClient::RESULT_FAILED) {
            Recipients::where('history_id', $this->blast->id)
                ->whereIn('contact_id', $contactIds)
                ->update(['sync_status' => 'failed']);

            Log::error('Recipient chunk rejected', ['blast_id' => $this->blast->id, 'response' => $response]);
            $this->fail();
            return;
        }

        $this->release($this->backoff[$this->attempts() - 1] ?? end($this->backoff));
    }
}