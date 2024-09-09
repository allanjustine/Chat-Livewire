<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupChatReaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function groupChatContent()
    {
        return $this->belongsTo(GroupChatContent::class);
    }

    public function emoji()
    {
        return $this->belongsTo(Emoji::class);
    }
}
