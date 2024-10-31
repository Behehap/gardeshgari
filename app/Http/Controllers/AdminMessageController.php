<?php

namespace App\Http\Controllers;

use App\DTOs\BaseDto;
use App\DTOs\BaseDtoStatusEnum;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMessageController extends Controller
{
    public function store(StoreMessageRequest $request, Ticket $ticket)
    {
        if($ticket->is_locked){
            return response()->json(new BaseDto(BaseDtoStatusEnum::ERROR, 'ticket is locked'), 403);
        }

        if(($ticket->is_resolved)) {
            return response()->json(new BaseDto(BaseDtoStatusEnum::ERROR, 'waiting for user message'), 403);
        }

        $validated = $request->validated();

        $message = $ticket->messages()->create([
            'message' => $validated['message'],
            'sender_id' => Auth::id(),
            'sender_name' => 'Admin',
        ]);
        $ticket->is_resolved = true;
        $ticket->status = 'replied';
        $ticket->save();
        return response()->json([new BaseDto(BaseDtoStatusEnum::OK,"message send",
            $message)], 201);
    }
}
