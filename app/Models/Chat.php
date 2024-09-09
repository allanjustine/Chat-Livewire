<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Chat extends Model
{
    use HasFactory, HasApiTokens;

    protected $guarded = [];

    protected $casts = [
        'attachment' => 'array',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function chatReactions()
    {
        return $this->hasMany(ChatReaction::class);
    }
}
