<?php

namespace App\Jobs;

use App\Models\History;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\RemoteApiClient;

class PushBlastToServerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public array $backoff = [10, 30, 60, 120, 300];

    public function __construct(public Model $blast) {}

    public function handle(): void
    {
        if (in_array($this->blast->sync_status, [History::SYNC_STATUS_BLAST_SYNCED, History::SYNC_STATUS_SYNCED])) {
            return;
        }

        $token = $this->blast->user->remote_token;

        if (!$token) {
            Log::error('No remote token for blast owner', ['blast_id' => $this->blast->id]);
            $this->fail();
            return;
        }

        $response = RemoteApiClient::post($this->blast->user, '/api/blasts', [
            'template_id' => $this->blast->template_id,
            'message' => $this->blast->blast,
            'slug' => $this->blast->slug,
            'send_mode' => $this->blast->send_mode,
            'type' => $this->blast->type,
            'local_uuid' => $this->blast->uuid,
        ]);

        if ($response['result'] === RemoteApiClient::RESULT_SUCCESS) 
        {
            $this->blast->update([
                'remote_id' => $response['data']['blast_id'],
                'sync_status' => History::SYNC_STATUS_BLAST_SYNCED,
            ]);
            return;
        }

        if ($response['result'] === RemoteApiClient::RESULT_FAILED) 
        {
            $this->blast->update(['sync_status' => History::SYNC_STATUS_FAILED]);
            Log::error('Blast rejected by server', ['blast_id' => $this->blast->id, 'response' => $response]);
            $this->fail();
            return;
        }

        if ($response['result'] === RemoteApiClient::RESULT_FAILED) 
        {
            $this->release($this->backoff[$this->attempts() - 1] ?? 300);
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->blast->update(['sync_status' => History::SYNC_STATUS_FAILED]);
    }
}