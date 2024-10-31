<?php

namespace App\Models;

use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
//use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'priority',
        'is_resolved'
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'is_resolved' => 'boolean',
    ];

    protected $hidden = [
      'updated_at',
    ];

    public function sender() : BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function messages() : HasMany
    {
        return $this->hasMany(Message::class, 'ticket_id');
    }


    public function scopeOwner(Builder $query)
    {
        return $query->where('sender_id', Auth::id());
    }

//    protected static function booted() : void
//    {
//        if(!(Auth::user()->is_admin)){
//            static::addGlobalScope('sender', function (Builder  $builder){
//                $builder->where('sender_id', Auth::id());
//            });
//        }
//    }

}
