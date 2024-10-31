<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketCollection;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Laravel\Prompts\Table;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Auth;


class UserTicketController extends Controller
{
    public function index(Request $request)
    {
        $tickets = QueryBuilder::for(Ticket::owner())
            ->allowedFilters(['priority', 'status'])
            ->where(function ($query) use ($request) {
                if ($request->has('priority')) {
                    $query->orWhere('priority', $request->priority);
                }
                if ($request->has('status')) {
                    $query->orWhere('status', $request->status);
                }
            })
            ->get();

//        $tickets = QueryBuilder::for(Ticket::class)
//            ->allowedFilters(['is_locked'])
//            ->defaultSort('created_at')
//            ->allowedSorts(['title', 'is_locked', 'created_at'])
//            ->paginate();

        return new TicketCollection($tickets);

    }

    public function show(Request $request, $ticket_id)
    {
        $ticket = Ticket::owner()->find($ticket_id);

        if(is_null($ticket)){
            return "article not found";
        }
        return new TicketResource($ticket);
    }

    public function store(StoreTicketRequest $request)
    {
        $validated = $request->validated();
        $message = $validated['message'];
        unset($validated['message']);
        $ticket = Auth::user()->tickets()->create($validated);

        $ticket->messages()->create([
            'message' => $message,
            'sender_id' => Auth::id(),
            'sender_name' => Auth::user()->name,
        ]);

        $ticket->status = 'waiting for reply';
        return new TicketResource($ticket);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        //
    }

    public function destroy(Request $request, $ticket_id)
    {
        $ticket = Ticket::owner()->find($ticket_id);

        if(is_null($ticket)){
            return "article not found";
        }
        $ticket->delete();

        return response()->noContent();
    }
}
