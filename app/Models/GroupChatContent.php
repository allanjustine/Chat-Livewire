<?php

namespace App\Models;

use App\Services\ParsableContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupChatContent extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'seen_by' => 'array',
        'deleted_by' => 'array',
        'attachment' => 'array'
    ];

    public function groupChat()
    {
        return $this->belongsTo(GroupChat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function groupChatSeenBies()
    {
        return $this->belongsToMany(User::class, 'group_chat_seen_bies')
            ->withTimestamps();
    }

    public function groupChatDeletedBies()
    {
        return $this->belongsToMany(User::class, 'group_chat_deleted_bies')
            ->withTimestamps();
    }

    public function GroupChatReactions(): HasMany
    {
        return $this->hasMany(GroupChatReaction::class)->chaperone();
    }

    public function groupChatReplies(): HasMany
    {
        return $this->hasMany(GroupChatReply::class)->chaperone();
    }

    public function getMessageAttribute(?string $value): ?string
    {
        $content = new ParsableContent();

        return $value !== null && $value !== '' && $value !== '0' ? $content->parse($value) : null;
    }
}
