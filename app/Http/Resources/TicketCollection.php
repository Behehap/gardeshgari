<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TicketCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);
        return $this->collection->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'title' => $ticket->title,
                'sender_id' => $ticket->sender_id,
                'sender_name' => $ticket->sender_name,
                'status' => $ticket->status,
                'created_at' => $ticket->created_at,
            ];
        })->toArray();
    }
}
