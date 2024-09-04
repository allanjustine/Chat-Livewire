<?php

namespace App\Livewire\Chats;

use App\Events\GroupChatIsSeen;
use App\Events\IsSeen as SeenNow;
use App\Events\GroupChatMessageSent;
use App\Models\Chat;
use App\Models\Emoji;
use App\Models\GroupChat;
use App\Models\GroupChatContent;
use App\Models\GroupChatMember;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class GroupConversation extends Component
{

    #[Title('Group Chats')]

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


    use WithFileUploads;

    public function loadMoreMessage()
    {
        $this->loadMore += $this->loadMorePlus;
    }

    #[On('echo:sendMessage,MessageSent')]
    #[On('echo:groupChatMessage,GroupChatMessageSent')]
    #[On('echo:gcIsSeen,GroupChatIsSeen')]
    public function groupChats()
    {
        $user = auth()->user();

        $convos = GroupChatContent::where('group_chat_id', $this->groupConvo->id)
            ->whereDoesntHave('groupChatDeletedBies', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->take($this->loadMore)
            ->get();

        foreach ($convos as $convo) {
            $convo->message = $this->escapeAndConvertUrlsToLinks($convo->message);
        }

        $users = User::where(function ($query) {
            $query->whereHas('senderChats', function ($query) {
                $query->where('sender_id', auth()->user()->id)
                    ->whereNotNull('message')
                    ->orWhereNotNull('attachment');
            })
                ->orWhereHas('receiverChats', function ($query) {
                    $query->where('receiver_id', auth()->user()->id)
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
            ->with(['senderChats' => function ($query) {
                $query->whereNotNull('message')
                    ->orWhereNotNull('attachment');
            }, 'receiverChats' => function ($query) {
                $query->whereNotNull('message')
                    ->orWhereNotNull('attachment');
            }])
            ->get()
            ->map(function ($user) {
                $allChats = $user->receiverChats->concat($user->senderChats)
                    ->filter(function ($chat) {
                        return $chat->receiver_id === auth()->user()->id || $chat->sender_id === auth()->user()->id;
                    })
                    ->sortByDesc('created_at');
                $latestChat = $allChats->first();
                $user->latest_chat_created_at = $latestChat->created_at ?? null;
                return $user;
            });
        $groupChats = GroupChat::with('groupChatMembers')
            ->whereHas('groupChatMembers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
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
            ->map(function ($groupChat) {
                $latestChat = $groupChat->groupChatContents->sortByDesc('created_at')->first();
                $groupChat->latest_chat_created_at = $latestChat->created_at ?? null;
                return $groupChat;
            });

        $combined = $users->map(function ($user) {
            $user->type = 'user';
            return $user;
        })->merge(
            $groupChats->map(function ($groupChat) {
                $groupChat->type = 'groupChat';
                return $groupChat;
            })
        )->sortByDesc('latest_chat_created_at');

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

        $emojis = Emoji::orderBy('id', 'asc')
            ->get();

        return compact('convos', 'combined', 'lastUnseen', 'emojis');
    }

    protected function escapeAndConvertUrlsToLinks($text)
    {
        $escapedText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        $urlPattern = '@(https?://[^\s<>"\'()]+\.[^\s<>"\'()]+)@i';
        $convertedText = preg_replace($urlPattern, '<a href="$1" target="_blank" style="color: #095ad2;" title="$1"><strong>$1</strong></a>', $escapedText);

        return $convertedText;
    }

    public function mount($groupChatToken)
    {
        $gc = GroupChat::where('group_chat_token', $groupChatToken)->first();

        $this->previous = URL::previous();

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
    }

    public function sendMessage()
    {
        if (empty($this->attachment)) {

            $this->validate([
                'message'           =>              ['required', 'min:1'],
                'attachment'        =>              ['nullable', 'max:102400']
            ]);
        }

        $groupId = $this->groupConvo->id;

        $notDuplicated = Str::random(10);

        $attachmentPaths = [];

        foreach ($this->attachment as $attach) {
            $originalName = $attach->getClientOriginalName();
            $originalExtension = $attach->getClientOriginalExtension();
            $fileName = $originalName . ' - ' . $notDuplicated . '.' . $originalExtension;
            $attachmentPaths[] = $attach->storeAs(path: 'public/gc/attachments', name: $fileName);
        }

        auth()->user()->update(['status' => 'online']);

        $chat = GroupChatContent::create([
            'user_id'           =>              auth()->user()->id,
            'group_chat_id'     =>              $groupId,
            'attachment'        =>              $attachmentPaths,
            'message'           =>              $this->message,
        ]);

        event(new GroupChatMessageSent($chat));

        $this->rowCount = 1;

        $this->reset(['message', 'attachment']);

        $this->dispatch('scrollBot');
    }

    public function sendLike()
    {

        $groupId = $this->groupConvo->id;

        $attachmentPaths = [];

        auth()->user()->update(['status' => 'online']);

        $chat = GroupChatContent::create([
            'user_id'           =>              auth()->user()->id,
            'group_chat_id'     =>              $groupId,
            'attachment'        =>              $attachmentPaths,
            'message'           =>              '(y)'
        ]);

        event(new GroupChatMessageSent($chat));
    }

    public function removeForEveryone($convoId)
    {
        $chat = GroupChatContent::find($convoId);

        if (!$chat) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'Failed to unsent',
            ]);

            return;
        } else {
            $chat->update([
                'status'        =>          'unsent'
            ]);

            event(new GroupChatMessageSent($chat));
        }
    }

    public function deleteForYou($convoId)
    {
        $chat = GroupChatContent::find($convoId);

        if (!$chat) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'Failed to delete',
            ]);

            return;
        } else {
            $chat->groupChatDeletedBies()->attach(auth()->user()->id);
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

        if (!$this->toEdit) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'No message found',
            ]);

            return;
        } elseif ($this->toEdit->user_id != auth()->user()->id) {
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


            event(new GroupChatMessageSent($this->toEdit));

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
            'gc_nickname'               =>               ['required', 'min:1'],
        ]);

        $this->editNickname->update([
            'gc_nickname'           =>              $this->gc_nickname
        ]);

        event(new GroupChatMessageSent($this->editNickname));

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
        $isSeen = Chat::where('sender_id', $userId)
            ->where('receiver_id', auth()->user()->id)
            ->where('is_seen', false)
            ->update([
                'is_seen'       =>      true
            ]);
        event(new SeenNow($isSeen));
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
                event(new GroupChatIsSeen($content));
            }
        }
    }

    public function leaveGc($groupConvoId)
    {
        $groupC = GroupChat::find($groupConvoId);

        if (!$groupC) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'You already leave this group chat',
            ]);
        } else {
            $groupC->groupChatMembers()->detach(auth()->user()->id);

            return $this->redirect('/chats', navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.chats.group-conversation', $this->groupChats());
    }
}
