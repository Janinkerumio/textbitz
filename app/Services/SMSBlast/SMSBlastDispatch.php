<?php

namespace App\Services\SMSBlast;

use Illuminate\Support\Facades\Bus;
use App\Jobs\PushBlastRecipientsJob;
use App\Jobs\PushBlastToServerJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Models\History;

trait SMSBlastDispatch
{
    protected static function dispatchBlastJob(Model $blast, Collection $recipients): void
    {
        $chunks = $recipients
                ->map(fn ($contact) => [
                    'local_contact_id' => $contact->id,
                    'phone_num' => $contact->phone_num,
                    'contact_name' => $contact->contact_name,
                ])
                ->chunk(200)
                ->values();

        Bus::chain([
            new PushBlastToServerJob($blast),
            ...$chunks->map(
                fn ($chunk) => new PushBlastRecipientsJob($blast, $chunk->values()->all())
            ),
        ])->catch(function (?\Throwable $e) use ($blast) {
            $blast->update(['status' => History::STATUS_FAILED]);
            Log::error('Blast sync chain failed', [
                'blast_id' => $blast->id,
                'error' => $e->getMessage() ?? 'Job failed manually (no exception provided)',
            ]);
        })->dispatch();
    }
}