<?php

namespace App\Services\SMSBlast;

use Illuminate\Http\Request;
use App\Models\History;
use App\Models\Template;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Services\Remote\ServerConnectivityService;

class SMSBlastService
{
    use SMSBlastRecipients, SMSBlastTextAndString, SMSBlastDispatch;

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

        return [
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

            if(ServerConnectivityService::isOnline())
            {
                self::dispatchBlastJob($blast, $recipients);
            }

            return [
                'success' => true,
                'queuedRecipients' => $createdRecipients,
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
}
