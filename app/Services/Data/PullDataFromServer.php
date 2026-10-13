<?php

namespace App\Services\Data;
use App\Models\History;
use App\Models\Recipients;
use App\Services\Remote\RemoteApiClient;
use App\Services\Remote\ServerConnectivityService;
use Illuminate\Support\Facades\Log;

class PullDataFromServer
{
    public static function reconcileInFlightBlasts()
    {
        if(!ServerConnectivityService::isOnline()) { return; }

        History::where('sync_status', History::SYNC_STATUS_BLAST_SYNCED)
            ->whereNotNull('remote_id')
            ->each(function (History $blast) {
                $response = RemoteApiClient::get($blast->user, "/api/blast/{$blast->remote_id}/status");

                if ($response['result'] !== RemoteApiClient::RESULT_SUCCESS) {
                    return;
                }
            
                $remoteStatus = $response['data']['status'] ?? null;
            
                if ($remoteStatus === 'delivered' || $remoteStatus === 'sent') 
                {
                    $blast->update([
                        'status' => History::STATUS_SENT,
                        'sync_status' => History::SYNC_STATUS_SYNCED,
                    ]);
                }
                elseif ($remoteStatus === 'failed') 
                {
                    $blast->update([
                        'status' => History::STATUS_FAILED,
                        'sync_status' => History::SYNC_STATUS_SYNCED,
                    ]);
                    Log::warning('Server reported blast delivery failure', ['blast_id' => $blast->id]);
                }

                foreach($response['data']['recipients'] ?? [] as $recipientNewStatus)
                {
                    Recipients::where('blast_id', $blast->id)
                        ->update([
                            'status' => $recipientNewStatus['status'],
                            'sent_at' => $recipientNewStatus['sent_at'],
                            'error_message' => $recipientNewStatus['error_message']
                        ]);
                }
            });
    }
}
