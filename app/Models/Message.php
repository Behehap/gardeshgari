<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'sender_id',
        'sender_name'
    ];
    protected $hidden = [
        'updated_at',
    ];

    public function sender() : BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function ticket() : BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
