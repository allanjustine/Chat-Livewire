<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'user_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'     =>          'datetime',
            // 'date_of_birth'         =>          'datetime',
            'password'              =>          'hashed',
        ];
    }

    public function senderChats(): HasMany
    {
        return $this->hasMany(Chat::class, 'sender_id')
            ->where('status', '!=', 'removed')
            ->where('receiver_id', auth()->user()->id)
            ->where('deleted_by_receiver', false)
            ->chaperone();
    }
    public function receiverChats(): HasMany
    {
        return $this->hasMany(Chat::class, 'receiver_id')
            ->where('status', '!=', 'removed')
            ->where('sender_id', auth()->user()->id)
            ->where('deleted_by_sender', false)
            ->chaperone();
    }

    public function unseenSenderChats(): HasMany
    {
        return $this->hasMany(Chat::class, 'sender_id')
            ->where('receiver_id', auth()->user()->id)
            ->where('is_seen', false)
            ->where('deleted_by_receiver', false)
            ->chaperone();
    }

    public function groupChats()
    {
        return $this->belongsToMany(GroupChat::class, 'group_chat_members')
            ->withPivot('gc_nickname')
            ->withTimestamps();
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class)->chaperone();
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class)->chaperone();
    }

    public function chatReactions(): HasMany
    {
        return $this->hasMany(ChatReaction::class)->chaperone();
    }

    public function GroupChatReactions(): HasMany
    {
        return $this->hasMany(GroupChatReaction::class)->chaperone();
    }

    public function chatReplies(): HasMany
    {
        return $this->hasMany(ChatReply::class)->chaperone();
    }

    public function groupChatReplies(): HasMany
    {
        return $this->hasMany(GroupChatReply::class)->chaperone();
    }
}
