<?php

namespace App\Livewire\Chats;

use App\Events\AddMemberToGroupChat;
use App\Events\GroupChatIsSeen;
use App\Events\IsSeen as SeenNow;
use App\Events\GroupChatMessageSent;
use App\Models\Chat;
use App\Models\Emoji;
use App\Models\GroupChat;
use App\Models\GroupChatContent;
use App\Models\GroupChatMember;
use App\Models\GroupChatReaction;
use App\Models\GroupChatReply;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class GroupConversation extends Component
{

    public $groupConvo;
    public $search = '';
    public $rowCount = 1;
    public $previous;
    public $toEdit;
    public $messageEdit;
    public $message = '';
    public $attachment = [];
    public $activeImageIndex = 0;
    public $messageCount;
    public $loadMore = 10;
    public $loadMorePlus = 10;
    public $editNickname;
    public $gc_nickname;
    public $inputEditNickname = false;
    public $toSeen;
    public $emojis = [];
    public $member = [];
    public $search_member = '';
    public $addedMember = '';
    public $idFromChat;
    public $isReply;
    public $replyContent;
    public $unsentReply;
    public $authId;


    use WithFileUploads;

    public function loadMoreMessage()
    {
        $this->loadMore += $this->loadMorePlus;
    }

    #[On('echo:sendMessage.{authId},MessageSent')]
    #[On('echo:groupChatMessage.{authId},GroupChatMessageSent')]
    #[On('echo:gcIsSeen.{authId},GroupChatIsSeen')]
    #[On('echo:AddMemberToGcSuccess,AddMemberToGroupChat')]
    public function groupChats()
    {
        $user = auth()->user();

        $convos = GroupChatContent::with([
            'user',
            'groupChatSeenBies',
            'groupChatReactions.user',
            'groupChatReactions.user.groupChats',
            'groupChatReactions.emoji',
            'groupChatReplies.user',
            'groupChatReplies.fromGroupChatContent' => function ($query) use ($user) {
                $query->whereDoesntHave('groupChatDeletedBies', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            },
            'groupChatReplies.groupChatContent'
        ])
            ->where('group_chat_id', $this->groupConvo->id)
            ->whereDoesntHave('groupChatDeletedBies', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->take($this->loadMore)
            ->get();

        $this->messageCount = GroupChatContent::where('group_chat_id', $this->groupConvo->id)
            ->whereDoesntHave('groupChatDeletedBies', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->count();

        $gcCountUnread = GroupChat::where('id', $this->groupConvo->id)
            ->withCount(['groupChatContents as unseen_count' => function ($query) use ($user) {
                $query->where('user_id', '!=', $user->id)
                    ->whereDoesntHave('groupChatSeenBies', function ($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id);
                    });
            }])
            ->first();

        $this->toSeen = $gcCountUnread->unseen_count;

        $unseenCount = GroupChatContent::where('group_chat_id', $this->groupConvo->id)
            ->whereDoesntHave('groupChatDeletedBies', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereDoesntHave('groupChatSeenBies', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $lastUnseen = $unseenCount->where('user_id', '!=', $user->id)->last();

        $emojiReaction = Emoji::whereIn('id', [5, 8, 14, 51, 52, 155, 1042])
            ->get();

        return compact('convos', 'lastUnseen', 'emojiReaction');
    }

    public function groupChatList()
    {
        $user = auth()->user();

        $users = User::where(function ($query) use ($user) {
            $query->whereHas('senderChats', function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->whereNotNull('message')
                    ->orWhereNotNull('attachment');
            })
                ->orWhereHas('receiverChats', function ($query) use ($user) {
                    $query->where('receiver_id', $user->id)
                        ->whereNotNull('message')
                        ->orWhereNotNull('attachment');
                });
        })
            ->where(function ($query) {
                if ($this->search) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                }
            })
            ->withCount(['unseenSenderChats' => function ($query) {
                $query->where('is_seen', false);
            }])
            ->with(['senderChats', 'receiverChats'])
            ->get()
            ->map(function ($user) {
                $allChats = $user->receiverChats->concat($user->senderChats)
                    ->filter(function ($chat) use ($user) {
                        return $chat->receiver_id === $user->id || $chat->sender_id === $user->id;
                    })
                    ->sortByDesc('created_at');
                $latestChat = $allChats->first();
                $user->latest_chat_created_at = $latestChat->created_at ?? null;
                return $user;
            });
        $groupChats = GroupChat::whereHas('groupChatMembers', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('groupChatMembers')
            ->where(function ($query) {
                $query->where('group_chat_name', 'like', '%' . $this->search . '%');
            })
            ->withCount(['groupChatContents as unseen_count' => function ($query) use ($user) {
                $query->where('user_id', '!=', $user->id)
                    ->whereDoesntHave('groupChatSeenBies', function ($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id);
                    });
            }])
            ->whereHas('groupChatContents')
            ->get()
            ->unique('id')
            ->map(function ($groupChat) {
                $latestChat = $groupChat->groupChatContents->sortByDesc('created_at')->first();
                $groupChat->latest_chat_created_at = $latestChat->created_at ?? null;
                return $groupChat;
            });

        // $combined = $users->map(function ($user) {
        //     $user->type = 'user';
        //     return $user;
        // })->merge(
        //     $groupChats->map(function ($groupChat) {
        //         $groupChat->type = 'groupChat';
        //         return $groupChat;
        //     })
        // )->sortByDesc('latest_chat_created_at');

        $allChats = $users->concat($groupChats)
            ->sortByDesc('latest_chat_created_at');


        $allUsers = User::whereNotIn('id', function ($query) {
            $query->select('user_id')
                ->from('group_chat_members')
                ->where('group_chat_id', $this->groupConvo->id);
        })
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search_member . '%');
            })
            ->get();

        return compact('allChats', 'allUsers');
    }

    public function mount($groupChatToken)
    {
        $gc = GroupChat::where('group_chat_token', $groupChatToken)->first();

        $this->previous = URL::previous();

        $this->authId = auth()->user()->id;

        if (!$gc) {
            $this->redirect($this->previous, navigate: true);
        } else {
            $user = auth()->user();
            $isMember = $gc->groupChatMembers->contains($user);

            if (!$isMember) {
                $this->redirect('/chats', navigate: true);
            }
            $this->groupConvo = $gc;
        }

        $this->emojis = Emoji::orderBy('id', 'asc')
            ->get();
    }

    public function sendMessage()
    {
        $user = auth()->user();
        $groupId = $this->groupConvo->id;

        $isMember = GroupChatMember::where('user_id', $user->id)
            ->where('group_chat_id', $groupId)
            ->exists();

        if (!$isMember) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'Failed to send, You are not a member on this group chat',
            ]);

            return;
        }

        if (empty($this->attachment)) {

            $this->validate([
                'message'           =>              ['required', 'min:1'],
                'attachment'        =>              ['nullable', 'max:5120']
            ]);
        }

        $notDuplicated = Str::random(10);

        $attachmentPaths = [];

        foreach ($this->attachment as $attach) {
            $originalName = $attach->getClientOriginalName();
            $originalExtension = $attach->getClientOriginalExtension();
            $fileName = $originalName . ' - ' . $notDuplicated . '.' . $originalExtension;
            $attachmentPaths[] = $attach->storeAs(path: 'public/gc/attachments', name: $fileName);
        }


        $user->update(['status' => 'online']);

        $chat = GroupChatContent::create([
            'user_id'           =>              $user->id,
            'group_chat_id'     =>              $groupId,
            'attachment'        =>              $attachmentPaths,
            'message'           =>              $this->message,
        ]);

        if ($this->isReply) {
            GroupChatReply::create([
                'user_id'                           =>            $user->id,
                'from_group_chat_content_id'        =>            $this->idFromChat,
                'group_chat_content_id'             =>            $chat->id
            ]);

            $this->idFromChat = null;

            $this->isReply = false;

            $this->replyContent = '';

            $this->unsentReply = '';
        }

        $member = $this->groupConvo->groupChatMembers;

        event(new GroupChatMessageSent($member));

        $this->rowCount = 1;

        $this->reset(['message', 'attachment']);

        $this->dispatch('scrollBot');
    }

    public function sendLike()
    {
        $user = auth()->user();

        $groupId = $this->groupConvo->id;

        $attachmentPaths = [];

        $user->update(['status' => 'online']);

        $chat = GroupChatContent::create([
            'user_id'           =>              $user->id,
            'group_chat_id'     =>              $groupId,
            'attachment'        =>              $attachmentPaths,
            'message'           =>              '(y)'
        ]);

        if ($this->isReply) {
            GroupChatReply::create([
                'user_id'                           =>            $user->id,
                'from_group_chat_content_id'        =>            $this->idFromChat,
                'group_chat_content_id'             =>            $chat->id
            ]);

            $this->idFromChat = null;

            $this->isReply = false;

            $this->replyContent = '';

            $this->unsentReply = '';
        }

        $member = $this->groupConvo->groupChatMembers;

        event(new GroupChatMessageSent($member));

        $this->dispatch('scrollBot');
    }

    public function removeForEveryone($convoId)
    {
        $userId = auth()->user()->id;

        $chat = GroupChatContent::find($convoId);

        if (!$chat || $chat->user_id !== $userId) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'Failed to unsent',
            ]);

            return;
        } else {
            $chat->update([
                'status'        =>          'unsent'
            ]);

            $member = $this->groupConvo->groupChatMembers;

            event(new GroupChatMessageSent($member));
        }
    }

    public function deleteForYou($convoId)
    {
        $user = auth()->user();

        $chat = GroupChatContent::find($convoId);

        if (!$chat) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'Failed to delete',
            ]);

            return;
        } else {
            $chat->groupChatDeletedBies()->attach($user->id);
        }
    }

    public function editMessage($id)
    {
        $convoToEdit = GroupChatContent::find($id);

        $this->toEdit = $convoToEdit;

        $this->messageEdit = $convoToEdit->message;
    }

    public function update()
    {
        $user = auth()->user();

        if (!$this->toEdit) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'No message found',
            ]);

            return;
        } elseif ($this->toEdit->user_id != $user->id) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'You cannot update users message',
            ]);

            return;
        } else {
            $this->validate([
                'messageEdit'       =>          ['required', 'min:1']
            ]);

            $this->toEdit->update([
                'message'           =>              $this->messageEdit
            ]);


            $member = $this->groupConvo->groupChatMembers;

            event(new GroupChatMessageSent($member));

            $this->dispatch('closeModal');

            return;
        }
    }

    public function removeTempUrlImg($index)
    {
        unset($this->attachment[$index]);

        $this->attachment = array_values($this->attachment);
    }

    public function clearAllAttachments()
    {
        $this->attachment = [];
    }

    public function setActiveImage($index)
    {
        $this->activeImageIndex = $index;
    }

    public function updatedMessage($value)
    {
        $lines = substr_count($value, "\n") + 1;
        $this->rowCount = min(max($lines, 1), 5);
    }

    public function nicknameEdit($memberId)
    {
        $userNickname = GroupChatMember::find($memberId);

        $this->editNickname = $userNickname;
        $this->gc_nickname = $userNickname->gc_nickname ?: $userNickname->user->name;


        $this->inputEditNickname = true;
    }

    public function updateGcNickname()
    {
        $this->validate([
            'gc_nickname'               =>               ['required', 'min:1', 'max:20'],
        ]);

        $this->editNickname->update([
            'gc_nickname'           =>              $this->gc_nickname
        ]);

        $member = $this->groupConvo->groupChatMembers;

        event(new GroupChatMessageSent($member));

        $this->dispatch('toastr', [
            'type'          =>          'success',
            'message'       =>          'You successfully update ' . $this->editNickname->user->name . ' nickname',
        ]);

        $this->inputEditNickname = false;
    }

    public function closeNicknameEdit()
    {
        $this->inputEditNickname = false;
    }

    public function seen($userId)
    {
        $userReceiverId = auth()->user()->id;

        $isSeen = Chat::where('sender_id', $userId)
            ->where('receiver_id',  $userReceiverId)
            ->where('is_seen', false)
            ->update([
                'is_seen'       =>      true
            ]);
        event(new SeenNow($userId, $userReceiverId));
    }

    public function gcSeen($gcId)
    {
        $user = auth()->user();
        $groupChat = GroupChat::find($gcId);

        if (!$groupChat) {
            return;
        }

        foreach ($groupChat->groupChatContents as $content) {
            if ($content->user_id != $user->id && !$content->groupChatSeenBies()->where('user_id', $user->id)->exists()) {
                $content->groupChatSeenBies()->attach($user->id);
            }
        }

        $member = $groupChat->groupChatMembers;

        event(new GroupChatIsSeen($member));
    }

    public function leaveGc($groupConvoId)
    {
        $userId = auth()->user()->id;

        $groupC = GroupChat::find($groupConvoId);

        if (!$groupC) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'You already leave this group chat',
            ]);
        } else {
            $groupC->groupChatMembers()->detach($userId);

            return $this->redirect('/chats', navigate: true);
        }
    }

    public function handleEmojiClick($emojiId, $convoId)
    {
        $user = auth()->user();

        $reaction = GroupChatReaction::where('user_id', $user->id)
            ->where('emoji_id', $emojiId)
            ->where('group_chat_content_id', $convoId)
            ->first();

        if ($reaction) {
            $reaction->delete();

            $remainingReactions = GroupChatReaction::where('group_chat_content_id', $convoId)->count();

            if ($remainingReactions === 0) {
                $this->dispatch('closeModal', ['convoId' => $convoId]);
            }
        } else {
            GroupChatReaction::updateOrCreate([
                'user_id'                           =>              $user->id,
                'emoji_id'                          =>              $emojiId,
                'group_chat_content_id'             =>              $convoId
            ]);
        }

        $member = $this->groupConvo->groupChatMembers;

        event(new GroupChatMessageSent($member));

    }

    public function addMemberToGc()
    {

        $this->validate([
            'member'            =>              ['required']
        ]);

        $this->groupConvo->groupChatMembers()->attach($this->member);

        $this->dispatch('toastr', [
            'type'          =>          'success',
            'message'       =>          count($this->member) . (count($this->member) <= 1 ? ' user added successfully' : ' users added successfully'),
        ]);

        event(new AddMemberToGroupChat($this->member));


        $this->reset(['member', 'search_member']);

        $this->dispatch('closeModal');
    }

    public function replyToChat($convoId)
    {
        $reply = GroupChatContent::find($convoId);

        if (!$reply) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'Cannot reply message not found'
            ]);

            return;
        }

        $this->idFromChat = $convoId;
        $this->isReply = true;
        $this->replyContent = $reply->message;
        $this->unsentReply = $reply->status;
    }

    public function cancelReply()
    {
        $this->isReply = false;
        $this->idFromChat = null;
        $this->replyContent = '';
        $this->unsentReply = '';
    }

    public function render()
    {
        return view(
            'livewire.chats.group-conversation',
            $this->groupChats(),
            $this->groupChatList()
        )->title('Group Chat' . ' - ' . $this->groupConvo->group_chat_name);
    }
}
