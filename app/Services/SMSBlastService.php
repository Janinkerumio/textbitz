<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\History;
use App\Models\Template;
use App\Models\Contact;
use App\Models\CorporateInfo;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SMSBlastService
{
    public static function processBlastRequest(array $data, Request $request)
    {
        $recipients = self::getRecipientsId($data, $request->query('select_all', false));
        $slug = self::makeSlug($data);

        $castRequests = [
            'template_id' => $data['template_id'],
            'blast' => $data['message'],
            'status' => History::STATUS_DRAFT,
            'slug' => $slug,
            'total_recipients' => count($recipients),
            'type' => $data['type'] ?? config('services.sms_blast.default.type'),
            'send_mode' => $data['send_mode'] ?? config('services.sms_blast.default.send_mode'),
        ];

        $blast = History::where('slug', $data['slug'])->first();

        if($blast)
        {
            $blast->update($castRequests);
        } else
        {
            $blast = History::create($castRequests);
        }

        return $blast;
    }

    public static function sendBlast(mixed $blast, array $recipients): array
    {
        return [

        ];
    }


    public static function scheduleBlast(mixed $blast, array $recipients, string $date): array
    {
        return [

        ];
    }

    public static function prepareMessage(string $message, mixed $recipient)
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

    protected static function makeSlug(array $data)
    {
        if(empty($data['slug']))
        {
            $template = Template::findByHashId($data['template_id']);
            
            return Str::slug($template->title);
        }
        
        return $data['slug'];
    }

    protected static function getRecipientsId(array $data, bool $selectAll)
    {
        $recipients = $data['recipients'] ?? [];

        if($selectAll)
        {
            $all = Contact::select('id')->get()->map(function ($contact) {
                $contact->hash_id = Hashids::encode($contact->id);
                return $contact;
            });
            $recipients = $all->pluck('hash_id')->diff($data['excludedRecipients'])->values();
        }

        return $recipients;
    }
}
