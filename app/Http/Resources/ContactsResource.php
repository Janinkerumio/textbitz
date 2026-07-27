<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->hash_id,
            'phone_num' => $this->phone_num,
            'contact_name' => $this->contact_name,
            'tags' => $this->tags,
        ];
    }
}
