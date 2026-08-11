<?php

namespace App\Services\SMSBlast;

use App\Models\Contact;
use App\Models\CorporateInfo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Template;

trait SMSBlastTextAndString
{
    protected static function prepareMessage(string $message, mixed $recipient): string
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
}