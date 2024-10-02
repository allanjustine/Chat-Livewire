<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Emoji extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function chatReactions(): HasMany
    {
        return $this->hasMany(ChatReaction::class)->chaperone();
    }

    public function GroupChatReactions(): HasMany
    {
        return $this->hasMany(GroupChatReaction::class)->chaperone();
    }
}
