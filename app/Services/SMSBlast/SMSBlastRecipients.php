<?php

namespace App\Services\SMSBlast;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Models\Recipients;
use App\Models\Contact;
use Vinkla\Hashids\Facades\Hashids;

trait SMSBlastRecipients
{
    protected static function createRecipients(Model $blast, Collection $recipients): int
    {
        $newRecipients = [];
        $count = 0;

        foreach($recipients as $recipient)
        {
            $newRecipients[] = [
                'history_id' => $blast->id,
                'contact_id' => $recipient->id,
                'name' => $recipient->contact_name,
                'mobile_num' => $recipient->phone_num,
                'status' => Recipients::STATUS_QUEUED,
                'sync_status' => Recipients::SYNC_STATUS_PENDING
            ];

            $count++;
        }

        if(!empty($newRecipients))
        {
            Recipients::insert($newRecipients);
        }

        return $count;
    }

    protected static function getRecipientsId(array $data, bool $selectAll): Collection
    {
        $recipients = $data['recipients'] ?? [];

        if($selectAll)
        {
            $all = Contact::initiateQuery()->select('id')->get()->map(function ($contact) {
                $contact->hash_id = Hashids::encode($contact->id);
                return $contact;
            });
            $recipients = $all->pluck('hash_id')->diff($data['excludedRecipients'])->values();
        }

        return collect($recipients)
                ->map(fn ($hash_id) => Hashids::decode($hash_id)[0] ?? null)
                ->filter()
                ->values();
    }

    protected static function getRecipientsContact(array $recipientIds = []): Collection
    {
        if(!empty($recipientIds))
        {
            return Contact::initiateQuery()
                        ->whereIn('id', $recipientIds)
                        ->whereNotNull('phone_num')
                        ->get();
        }

        $recipients = Contact::initiateQuery()
                            ->whereNotNull('phone_num')
                            ->get();

        if (empty($recipients)) {
            return collect();
        }

        return $recipients;
    }
}