<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipientResource extends JsonResource
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
            'name' => $this->name,
            'mobile_num' => $this->mobile_num,
            'status' => $this->status,
            'error_message' => $this->error_message,
            'sent_at' => $this->sent_at
        ];
    }
}
