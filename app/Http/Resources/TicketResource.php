<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        $data = parent::toArray($request);
//        $data['is_locked'] = $this->is_locked ? "locked" : "unlocked";
//        return $data;
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'priority' => $this->priority,
            'message' => MessageResource::collection($this->messages),
            'create_at' => $this->created_at,
        ];
    }
}
