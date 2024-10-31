<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * فیلدهایی که قابل مقداردهی هستند.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
    ];

    /**
     * فیلدهای مخفی.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
        'phone',
        'updated_at'
    ];

    public function tickets() : HasMany
    {
        return $this->hasMany(Ticket::class, 'sender_id');
    }

    public function messages() : HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function articles() : HasMany
    {
        return $this->hasMany(Article::class, 'creator_id');
    }
}
