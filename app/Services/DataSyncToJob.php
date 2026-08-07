<?php

namespace App\Services;

use App\Jobs\PushBlastToServerJob;
use App\Jobs\PushBlastRecipientsJob;
use App\Models\History;
use App\Models\Recipients;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class DataSyncToJob
{
    public static function retryPendingSyncs(): void
    {
        foreach (self::syncTasks() as $name => $task) {
            try {
                $task();
            } catch (\Throwable $e) {
                Log::error("Sync retry failed for [{$name}]", ['error' => $e->getMessage()]);
            }
        }
    }

    protected static function syncTasks(): array
    {
        return [
            'blasts' => fn () => self::retryBlasts(),
            'recipients' => fn () => self::retryRecipients(),
            // 'templates' => fn () => self::retryTemplates(),
        ];
    }

    protected static function retryBlasts(bool $allowFailed = false): void
    {
        $syncs = [History::SYNC_STATUS_PENDING];

        if ($allowFailed) {
            $syncs[] = History::SYNC_STATUS_FAILED;
        }

        History::whereIn('sync_status', $syncs)
            ->whereNotIn('id', self::blastIdsAwaitingRecipientChain($syncs))
            ->each(fn (History $blast) => PushBlastToServerJob::dispatch($blast));
    }

    protected static function retryRecipients(bool $allowFailed = true): void
    {
        $syncs = [Recipients::SYNC_STATUS_PENDING];

        if ($allowFailed) {
            $syncs[] = Recipients::SYNC_STATUS_FAILED;
        }

        $retryableBlastSyncs = [History::SYNC_STATUS_PENDING];

        Recipients::whereIn('sync_status', $syncs)
            ->get()
            ->groupBy('history_id')
            ->each(function ($group, $historyId) use ($retryableBlastSyncs) {
                $blast = History::find($historyId);

                if (!$blast) {
                    return;
                }

                $recipientJobs = $group
                    ->map(fn ($r) => [
                        'local_contact_id' => $r->contact_id,
                        'phone_num' => $r->mobile_num,
                        'contact_name' => $r->name,
                    ])
                    ->chunk(200)
                    ->map(fn ($chunk) => new PushBlastRecipientsJob($blast, $chunk->values()->all()))
                    ->values();

                if (!$blast->remote_id) {
                    if (!in_array($blast->sync_status, $retryableBlastSyncs, true)) {
                        // Blast is in a terminal state we don't auto-retry (e.g. failed);
                        // its recipients stay pending until the blast itself is resolved.
                        return;
                    }

                    Bus::chain([
                        new PushBlastToServerJob($blast),
                        ...$recipientJobs,
                    ])->catch(function (\Throwable $e) use ($blast) {
                        $blast->update(['sync_status' => History::SYNC_STATUS_FAILED]);
                        Log::error('Blast resync chain failed', [
                            'blast_id' => $blast->id,
                            'error' => $e->getMessage(),
                        ]);
                    })->dispatch();

                    return;
                }

                $recipientJobs->each(fn (PushBlastRecipientsJob $job) => dispatch($job));
            });
    }

    protected static function blastIdsAwaitingRecipientChain(array $blastSyncs): array
    {
        return Recipients::whereIn('sync_status', [Recipients::SYNC_STATUS_PENDING, Recipients::SYNC_STATUS_FAILED])
            ->whereHas('history', fn ($query) => $query->whereIn('sync_status', $blastSyncs)->whereNull('remote_id'))
            ->pluck('history_id')
            ->unique()
            ->all();
    }
}
