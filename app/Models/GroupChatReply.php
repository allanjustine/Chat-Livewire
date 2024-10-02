<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupChatReply extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function groupChatContent()
    {
        return $this->belongsTo(GroupChatContent::class, 'group_chat_content_id');
    }

    public function fromGroupChatContent()
    {
        return $this->belongsTo(GroupChatContent::class, 'from_group_chat_content_id');
    }
}
