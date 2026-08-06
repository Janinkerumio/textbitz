<?php

namespace App\Jobs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Recipients;
use App\Services\RemoteApiClient;

class PushBlastRecipientsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public array $backoff = [10, 30, 60, 120, 300];

    public function __construct(
        public Model $blast,
        public array $recipients,
    ) {}

    public function handle(): void
    {
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

        if ($response['result'] === RemoteApiClient::RESULT_FAILED) {
            Recipients::where('history_id', $this->blast->id)
                ->whereIn('contact_id', $contactIds)
                ->update(['sync_status' => 'failed']);

            Log::error('Recipient chunk rejected', ['blast_id' => $this->blast->id, 'response' => $response]);
            $this->fail();
            return;
        }

        if ($response['result'] === RemoteApiClient::RESULT_FAILED) 
        {
            $this->release($this->backoff[$this->attempts() - 1] ?? 300);
        }
    }
}