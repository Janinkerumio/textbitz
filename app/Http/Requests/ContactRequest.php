<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Contact;
use App\Rules\PhilippineMobileNumber;

class ContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        if ($this->isMethod('post')) {
            return true;
        }

        $contact = Contact::findByHashId($this->route('id'));

        return $contact && $contact->user_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $contact = $this->route('id')
                ? Contact::findByHashId($this->route('id'))
                : null;

        return [
            'contact_name' => ['required', 'string', 'max:100'],
            'phone_num' => [
                'required', 'string', 'max:20',
                Rule::unique('contacts', 'phone_num')
                    ->where('user_id', Auth::id())
                    ->ignore($contact?->id),
                new PhilippineMobileNumber
            ],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
        ];
    }
}
