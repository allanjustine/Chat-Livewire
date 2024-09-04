<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        ->withPivot(['gc_nickname', 'id'])
        ->withTimestamps();
    }

    public function groupChatContents()
    {
        return $this->hasMany(GroupChatContent::class);
    }
}
