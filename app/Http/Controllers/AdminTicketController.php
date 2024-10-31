<?php

namespace App\Http\Controllers;

use App\DTOs\BaseDto;
use App\DTOs\BaseDtoStatusEnum;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\TicketCollection;
use App\Http\Resources\TicketResource;
use App\Models\Article;
use App\Models\Ticket;
use Illuminate\Http\Request;

class AdminTicketController extends Controller
{

    public function index(Request $request)
    {

        $tickets = QueryBuilder::for(Ticket::class)
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

        return response()->json([new BaseDto(BaseDtoStatusEnum::OK,"tickets successful get",
            new TicketCollection($tickets))]);
//        return new TicketCollection(Ticket::all());
    }

    public function show(Request $request, Ticket $ticket)
    {
        $ticket->status = 'in progress';
        $ticket->save();
//        return new TicketResource($ticket);
        return response()->json([new BaseDto(BaseDtoStatusEnum::OK,"ticket successful get",
            new TicketResource($ticket))]);
    }



}
