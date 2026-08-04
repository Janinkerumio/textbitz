<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BlastRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if(!Auth::check())
        {
            return false;
        }

        if($this->method('post'))
        {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => 'required|string|max:255',
            'recipients' => 'sometimes|array',
            'recipients.*' => 'string|max:12',
            'excludedRecipients' => 'nullable|array',
            'excludedRecipients.*' => 'string|max:12',
            'template_id' => 'nullable|string|max:12',
            'title' => 'required|string|max:100',
            'type' => 'nullable|string|max:50',
            'slug' => 'nullable|string|max:50',
            'send_mode' => 'nullable|string|max:50',
            'scheduled_date' => 'nullable|date',
            'scheduled_time' => 'nullable|date_format:H:i',
        ];
    }
}
