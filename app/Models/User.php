<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

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

    public function senderChats()
    {
        return $this->hasMany(Chat::class, 'sender_id')
            ->where('status', '!=', 'removed')
            ->where('receiver_id', auth()->user()->id)
            ->where('deleted_by_receiver', false);
    }
    public function receiverChats()
    {
        return $this->hasMany(Chat::class, 'receiver_id')
            ->where('status', '!=', 'removed')
            ->where('sender_id', auth()->user()->id)
            ->where('deleted_by_sender', false);
    }

    public function unseenSenderChats()
    {
        return $this->hasMany(Chat::class, 'sender_id')
            ->where('receiver_id', auth()->user()->id)
            ->where('is_seen', false)
            ->where('deleted_by_receiver', false);
    }

    public function groupChats()
    {
        return $this->belongsToMany(GroupChat::class, 'group_chat_members')
            ->withPivot('gc_nickname')
            ->withTimestamps();
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
