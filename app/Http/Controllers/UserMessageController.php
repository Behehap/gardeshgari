<?php

namespace App\Http\Controllers;

use App\DTOs\BaseDto;
use App\DTOs\BaseDtoStatusEnum;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserMessageController extends Controller
{
    public function store(StoreMessageRequest $request, Ticket $ticket)
    {

        if ($ticket->sender_id != Auth::id()) {
            return response()->json(new BaseDto(BaseDtoStatusEnum::ERROR, 'unAuthorization'), 403);
        }

        if($ticket->is_locked){
            return response()->json(new BaseDto(BaseDtoStatusEnum::ERROR, 'ticket is locked'), 403);
        }

        if(!($ticket->is_resolved)) {
            return response()->json(new BaseDto(BaseDtoStatusEnum::ERROR, 'waiting for admin answer'), 403);
        }

        $validated = $request->validated();

        $message = $ticket->messages()->create([
            'message' => $validated['message'],
            'sender_id' => Auth::id(),
            'sender_name' => Auth::user()->name,
        ]);
        $ticket->is_resolved = false;
        $ticket->save();

        return response()->json($message, 201);
    }
}
