<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\History;
use App\Models\Template;
use App\Models\Contact;
use App\Models\Recipients;
use App\Models\CorporateInfo;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\RateLimiter;

class SMSBlastService
{
    public static function dispatchBlast(Model $blast): array
    {
        return [
            'success' => false,
        ];
    }

    public static function dispatchRecipient(Model $blast, Model $recipient): array
    {
        return [
            'success' => false,
        ];
    }

    public static function processBlastRequest(array $data, Request $request): array
    {
        $recipients = self::getRecipientsId($data, $request->query('select_all', false));
        $slug = self::makeSlug($data);
        $template = !$data['template_id'] ? null : Template::findByHashId($data['template_id']);

        $castRequests = [
            'user_id' => Auth::id(),
            'template_id' => $template?->id ?? null,
            'title' => $data['title'],
            'blast' => $data['message'],
            'status' => History::STATUS_DRAFT,
            'slug' => $slug,
            'recipients' => count($recipients->toArray()),
            'type' => $data['type'] ?? config('services.sms_blast.default.type'),
            'send_mode' => $data['send_mode'] ?? config('services.sms_blast.default.send_mode'),
        ];

        $blast = History::where('slug', $slug)->first();

        if($blast && $castRequests['type'] === 'automation') // use a const case for this on future versions
        {
            $blast->update($castRequests);
        } else
        {
            $blast = History::create($castRequests);
        }

        $result = self::dispatchBlast($blast);

        return [
            'status' => $result,
            'blast' => $blast,
            'recipients' => $recipients
        ];
    }

    public static function resolveSendMode(Model $blast, Collection $recipients, array $data): array
    {
        $dateTimeEx = null;

        if(!empty($data['scheduled_date']))
        {
            $dateTimeEx = $data['scheduled_time'] 
                                ? $data['scheduled_date'] . ' ' . $data['scheduled_time']
                                : $data['scheduled_time'];
        }

        $result = [
            'success' => false,
            'message' => 'Unkown error'
        ];

        switch($blast->send_mode)
        {
            case 'scheduled':
                $result = self::postScheduleBlast($blast, $recipients->toArray(), $dateTimeEx);
                break;
            case 'now':
                $result = self::postSendBlast($blast, $recipients->toArray());
                break;
            case 'alltimes':
                $result = ['success' => true];
                break;
        }

        return $result;
    }

    public static function postSendBlast(Model $blast, array $recipientIds): array
    {
        try
        {
            if($blast->status !== History::STATUS_QUEUED)
            {
                $blast->update(['status' => History::STATUS_QUEUED]);
            }

            $recipients = self::getRecipientsContact($recipientIds);

            if ($recipients->isEmpty()) {
                $blast->update(['status' => History::STATUS_FAILED]);

                return [
                    'success' => false, 
                    'message' => 'No recipients found'
                ];
            }

            $createdRecipients = self::createRecipients($blast, $recipients);
            $processedRecipients = 0;

            foreach ($recipients as $recipient) {
                RateLimiter::attempt(
                    key: "sms-blast:{$blast->id}",
                    maxAttempts: 100,
                    callback: function () use ($blast, $recipient, &$processedRecipients) {

                        $result = self::dispatchRecipient($blast, $recipient);

                        if ($result['success']) {
                            $processedRecipients++;
                        } else {
                            Log::warning('Recipient dispatch failed', [
                                'blast_id' => $blast?->id,
                                'recipient' => $recipient->id ?? $recipient,
                                'reason' => $result['error'] ?? 'unknown',
                            ]);
                        }
                    },
                    decaySeconds: 0.5
                );
            }

            $blast->update(['status' => $createdRecipients === $processedRecipients ? History::STATUS_QUEUED : History::STATUS_DRAFT]);

            return [
                'success' => true,
                'proccessedRecipients' => $processedRecipients,
                'blast' => $blast
            ];

        } catch (\Throwable $e) {
            report($e);
            $blast->update(['status' => History::STATUS_FAILED]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
        
    }

    public static function postScheduleBlast(Model $blast, array $recipients, string $date): array
    {
        return [

        ];
    }

    public static function prepareMessage(string $message, mixed $recipient): string
    {
        $contact = Contact::findOrFail($recipient->id);
        $business = CorporateInfo::where('user_id', Auth::id());

        $replacements = [
            '{name}' => $contact->contact_name,
            '{phone_number}' => $contact->phone_num,
            '{business_name}' => $business->business_name,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }

    protected static function makeSlug(array $data): string
    {

        if(!$data['template_id'])
        {
            return Str::slug($data['title']);
        }

        if(empty($data['slug']))
        {
            $template = Template::findByHashId($data['template_id']);
            
            return Str::slug($template->title);
        }
        
        return $data['slug'];
    }

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
                'status' => Recipients::STATUS_DRAFT,
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
