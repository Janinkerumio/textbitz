<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
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
            'template' => TemplateMiniResource::make($this->whenLoaded('template')),
            'blast' => $this->blast,
            'status' => $this->status,
            'recipients' => $this->recipients,
            'last_sent_at' => $this->last_sent_at,
        ];
    }
}
