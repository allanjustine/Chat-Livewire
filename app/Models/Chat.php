<?php

namespace App\Models;

use App\Services\ParsableContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function chatReactions(): HasMany
    {
        return $this->hasMany(ChatReaction::class)->chaperone();
    }

    public function chatReplies(): HasMany
    {
        return $this->hasMany(ChatReply::class)->chaperone();
    }

    public function getMessageAttribute(?string $value): ?string
    {
        $content = new ParsableContent();

        return $value !== null && $value !== '' && $value !== '0' ? $content->parse($value) : null;
    }
}
