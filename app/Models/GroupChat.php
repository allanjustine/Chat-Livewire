<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupChat extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'group_chat_token',
    ];

    public function groupChatMembers()
    {
        return $this->belongsToMany(User::class, 'group_chat_members')
        ->withPivot(['gc_nickname', 'id', 'is_admin'])
        ->withTimestamps();
    }

    public function groupChatContents(): HasMany
    {
        return $this->hasMany(GroupChatContent::class)->chaperone();
    }
}
