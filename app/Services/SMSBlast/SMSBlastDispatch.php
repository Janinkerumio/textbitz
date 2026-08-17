<?php

namespace App\Services\SMSBlast;

use Illuminate\Support\Facades\Bus;
use App\Jobs\PushBlastRecipientsJob;
use App\Jobs\PushBlastToServerJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Models\History;
use App\Models\Recipients;
use Illuminate\Support\Facades\Validator;
use App\Rules\PhilippineMobileNumber;

trait SMSBlastDispatch
{
    use SMSBlastTextAndString;

    protected static function dispatchBlastJob(Model $blast, Collection $recipients): void
    {
        $chunks = static::validateBeforeDispatch($blast, $recipients)
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

    protected static function validateBeforeDispatch(Model $blast, Collection $recipients): Collection
    {
        $validRecipients = collect();
        $invalidContactIds = [];

        foreach ($recipients as $contact) {
            $validator = Validator::make(
                ['phone_num' => $contact->phone_num, 'contact_name' => $contact->contact_name],
                [
                    'phone_num' => ['required', 'string', new PhilippineMobileNumber],
                    'contact_name' => ['required', 'string', 'max:180'],
                ]
            );

            if ($validator->fails()) {
                $invalidContactIds[] = $contact->id;
                Log::warning('Skipping invalid recipient before dispatch', [
                    'blast_id' => $blast->id,
                    'contact_id' => $contact->id,
                    'errors' => $validator->errors()->all(),
                ]);
                continue;
            }

            $validRecipients->push([
                'blast_id' => $blast->id,
                'local_contact_id' => $contact->id,
                'phone_num' => $contact->phone_num,
                'contact_name' => $contact->contact_name,
                'message' => self::prepareMessage($contact, $blast)
            ]);
        }

        if (!empty($invalidContactIds)) {
            Recipients::where('history_id', $blast->id)
                ->whereIn('contact_id', $invalidContactIds)
                ->update([
                    'sync_status' => Recipients::SYNC_STATUS_FAILED,
                    'status' => Recipients::STATUS_FAILED,
                    'error_message' => 'Invalid phone number or missing name',
                ]);
        }

        if ($validRecipients->isEmpty()) {
            Log::warning('No valid recipients to sync for blast', ['blast_id' => $blast->id]);
            return collect();
        }

        return $validRecipients;
    }
}