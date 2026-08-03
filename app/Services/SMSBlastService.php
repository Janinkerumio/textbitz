<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\History;
use App\Models\Template;
use App\Models\Contact;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Str;

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
            'type' => $data['type'] ?? null,
            'send_mode' => $data['send_mode'] ?? null,
        ];

        return $castRequests;

        // $blast = History::where('slug', $data['slug'])->first();

        // if($blast)
        // {
        //     $blast->update($castRequests);
        // } else
        // {
        //     $blast = History::create($castRequests);
        // }
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
