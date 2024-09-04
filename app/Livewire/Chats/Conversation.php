<?php

namespace App\Livewire\Chats;

use App\Events\GroupChatIsSeen;
use App\Events\MessageSent;
use App\Events\IsSeen as SeenNow;
use App\Models\Chat;
use App\Models\Emoji;
use App\Models\GroupChat;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Illuminate\Support\Str;

class Conversation extends Component
{
    #[Title('Chats')]

    public $search = '';
    public $userConvo;
    public $previous;
    public $toEdit;
    public $messageEdit;
    public $loadMore = 10;
    public $loadMorePlus = 10;
    public $messageCount;
    public $rowCount = 1;
    public $activeImageIndex = 0;
    public $toSeen;

    #[Validate(['nullable', 'max:102400'])]
    public $attachment = [];

    #[Validate(['required', 'min:1'])]
    public $message = '';

    use WithFileUploads;


    public function loadMoreMessage()
    {
        $this->loadMore += $this->loadMorePlus;
    }

    #[On('echo:groupChatMessage,GroupChatMessageSent')]
    #[On('echo:sendMessage,MessageSent')]
    #[On('echo:isSeen,IsSeen')]
    public function conversation()
    {
        $user = auth()->user();

        $userId = $user->id;

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

        $convos = Chat::with(['sender', 'receiver'])
            ->where(function ($query) use ($userId) {
                $query->where(function ($subQuery) use ($userId) {
                    $subQuery->where('sender_id', $this->userConvo->id)
                        ->where('receiver_id', $userId)
                        ->where('deleted_by_receiver', false);
                })->orWhere(function ($subQuery) use ($userId) {
                    $subQuery->where('sender_id', $userId)
                        ->where('receiver_id', $this->userConvo->id)
                        ->where('deleted_by_sender', false);
                });
            })
            ->where(function ($query) {
                $query->where('status', 'active')
                    ->orWhere('status', 'unsent');
            })
            ->orderBy('created_at', 'desc')
            ->take($this->loadMore)
            ->get();


        $userCountUnread = User::where('id', $this->userConvo->id)
            ->withCount(['unseenSenderChats' => function ($query) {
                $query->where('is_seen', false);
            }])
            ->first();

        $this->toSeen = $userCountUnread->unseen_sender_chats_count;

        foreach ($convos as $convo) {
            $convo->message = $this->escapeAndConvertUrlsToLinks($convo->message);
        }

        // foreach ($convos as $convo) {
        //     if (filter_var($convo->message, FILTER_VALIDATE_URL)) {
        //         $metadata = UrlHelper::getUrlMetadata($convo->message);
        //         $convo->metadata = $metadata;
        //     }
        // }

        $convosCount = Chat::with(['sender', 'receiver'])
            ->where(function ($query) use ($userId) {
                $query->where(function ($subQuery) use ($userId) {
                    $subQuery->where('sender_id', $this->userConvo->id)
                        ->where('receiver_id', $userId)
                        ->where('deleted_by_receiver', false);
                })->orWhere(function ($subQuery) use ($userId) {
                    $subQuery->where('sender_id', $userId)
                        ->where('receiver_id', $this->userConvo->id)
                        ->where('deleted_by_sender', false);
                });
            })
            ->where(function ($query) {
                $query->where('status', 'active')
                    ->orWhere('status', 'unsent');
            })->count();

        $this->messageCount = $convosCount;

        $lastUnseen = $convos->where('is_seen', false)->last();

        $emojis = Emoji::orderBy('id', 'asc')
            ->get();

        return compact('convos', 'lastUnseen',  'combined', 'emojis');
    }

    protected function escapeAndConvertUrlsToLinks($text)
    {
        $escapedText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        $urlPattern = '@(https?://[^\s<>"\'()]+\.[^\s<>"\'()]+)@i';
        $convertedText = preg_replace($urlPattern, '<a href="$1" target="_blank" style="color: #095ad2;" title="$1"><strong>$1</strong></a>', $escapedText);

        return $convertedText;
    }

    public function mount($userToken)
    {
        $convo = User::where('user_token', $userToken)

            ->withCount(['unseenSenderChats' => function ($query) {
                $query->where('is_seen', false);
            }])

            ->first();

        $this->previous = URL::previous();

        if (!$convo) {
            $this->redirect($this->previous, navigate: true);
        } else {
            $this->userConvo = $convo;
        }
    }

    #[On(['submitEnter'])]
    public function sendMessage()
    {
        if (empty($this->attachment)) {

            $this->validate();
        }

        $receiverId = $this->userConvo->id;
        $receiverDetails = $this->userConvo;

        $notDuplicated = Str::random(10);

        $attachmentPaths = [];

        foreach ($this->attachment as $attach) {
            $originalName = $attach->getClientOriginalName();
            $originalExtension = $attach->getClientOriginalExtension();
            $fileName = $originalName . ' - ' . $notDuplicated . '.' . $originalExtension;
            $attachmentPaths[] = $attach->storeAs(path: 'public/attachments', name: $fileName);
        }

        auth()->user()->update(['status' => 'online']);

        $chat = Chat::create([
            'sender_id'         =>              auth()->user()->id,
            'receiver_id'       =>              $receiverId,
            'attachment'        =>              $attachmentPaths,
            'message'           =>              $this->message,
            'is_seen'           =>              $receiverDetails->user_token === auth()->user()->user_token ? true : false,
        ]);

        event(new MessageSent($chat));

        $this->rowCount = 1;

        $this->reset(['message', 'attachment']);

        $this->dispatch('scrollBot');
    }

    public function sendLike()
    {

        $receiverId = $this->userConvo->id;
        $receiverDetails = $this->userConvo;

        $attachmentPaths = [];

        auth()->user()->update(['status' => 'online']);

        $chat = Chat::create([
            'sender_id'         =>              auth()->user()->id,
            'receiver_id'       =>              $receiverId,
            'attachment'        =>              $attachmentPaths,
            'message'           =>              '(y)',
            'is_seen'           =>              $receiverDetails->user_token === auth()->user()->user_token ? true : false,
        ]);

        event(new MessageSent($chat));
    }


    public function removeForEveryone($convoId)
    {
        $chat = Chat::find($convoId);

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

            event(new MessageSent($chat));
        }
    }

    public function deleteForYou($convoId)
    {
        $chat = Chat::find($convoId);
        $userId = auth()->user()->id;

        if (!$chat) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'Failed to delete',
            ]);

            return;
        } else {
            if ($chat->receiver_id === $userId) {
                $chat->update([
                    'deleted_by_receiver'       =>              true,
                ]);
            } elseif ($chat->sender_id === $userId) {
                $chat->update([
                    'deleted_by_sender'         =>              true,
                ]);
            }
            if ($chat->receiver_id === $userId && $chat->sender_id === $userId) {
                $chat->update([
                    'deleted_by_receiver'       =>              true,
                    'deleted_by_sender'         =>              true,
                ]);
            }
        }
    }

    // #[On(['submitEnter'])]
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

    public function deleteConversation($userId)
    {

        $currentUserId = auth()->user()->id;

        Chat::where('sender_id', $currentUserId)
            ->where('receiver_id', $userId)
            ->update(['deleted_by_sender' => true]);

        Chat::where('receiver_id', $currentUserId)
            ->where('sender_id', $userId)
            ->update(['deleted_by_receiver' => true]);

        // Chat::query()->update([
        //     'deleted_by_sender' =>  false,
        //     'deleted_by_receiver'   => false
        // ]);

        $this->dispatch('toastr', [
            'type'          =>          'success',
            'message'       =>          'Conversation deleted',
        ]);

        $this->redirect('/chats', navigate: true);
    }

    public function editMessage($id)
    {
        $convoToEdit = Chat::find($id);

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
        } elseif ($this->toEdit->sender_id != auth()->user()->id) {
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

            event(new MessageSent($this->toEdit));

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

    public function updatedMessage($value)
    {
        $lines = substr_count($value, "\n") + 1;
        $this->rowCount = min(max($lines, 1), 5);
    }

    public function setActiveImage($index)
    {
        $this->activeImageIndex = $index;
    }

    public function render()
    {
        return view('livewire.chats.conversation', $this->conversation());
    }
}
