<?php

namespace App\Services\SMSBlast;

use App\Models\Contact;
use App\Models\CorporateInfo;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Template;
use Illuminate\Database\Eloquent\Model;

trait SMSBlastTextAndString
{
    protected static function prepareMessage(Model $recipient, Model $blast): string
    {
        $replacements = static::defaultVariables($recipient, $blast);
        //future implementation would have dynamic variables based on a record

        return str_replace(array_keys($replacements), array_values($replacements), $blast->blast);
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

    private static function defaultVariables(Model $recipient, Model $blast): array
    {
        $contact = Contact::findOrFail($recipient->id);
        $business = CorporateInfo::where('user_id', $blast->user_id)->first();

        return [
            '{name}' => $contact->contact_name,
            '{phone_number}' => $contact->phone_num,
            '{business_name}' => $business->business_name ?? 'My Business',
            '{date}' => Carbon::now()->format('F j, Y')
        ];
    }
}