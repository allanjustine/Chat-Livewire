<?php

namespace App\Livewire\Chats;

use App\Events\GroupChatCreated;
use App\Events\GroupChatIsSeen;
use App\Events\IsSeen as SeenNow;
use App\Models\Chat;
use App\Models\GroupChat;
use App\Models\GroupChatContent;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Illuminate\Support\Str;
use Livewire\Component;

class Index extends Component
{
    #[Title('Chats')]

    public $previous;
    public $search = '';
    public $searched_gc = '';
    public $people = true;
    public $groupChat = false;
    public $group_chat_name;
    public $member = [];
    public $search_member = '';
    public $loadMore = 5;
    public $load = 20;
    public $usersCount = 0;

    public function loadMorePage()
    {
        $this->load += $this->loadMore;
    }

    #[On('echo:sendMessage,MessageSent')]
    #[On('echo:isSeen,IsSeen')]
    #[On('echo:groupChatMessage,GroupChatMessageSent')]
    #[On('echo:gcIsSeen,GroupChatIsSeen')]
    #[On('echo:gcCreated,GroupChatCreated')]
    #[On('echo:AddMemberToGcSuccess,AddMemberToGroupChat')]
    public function chats()
    {
        $user = auth()->user();

        $users = User::with(['senderChats', 'receiverChats'])->withCount('unseenSenderChats')->get()->sortByDesc(function ($user) {
            $allChats = $user->receiverChats->concat($user->senderChats)
                ->filter(function ($chat) {
                    return $chat->receiver_id === auth()->user()->id || $chat->sender_id === auth()->user()->id;
                })
                ->sortByDesc('created_at');

            $latestChat = $allChats->first();

            return $latestChat->created_at ?? null;
        })->take($this->load);

        $searched = User::where('name', 'like', '%' . $this->search . '%')
            ->get();


        $totalChats = Chat::where('receiver_id', $user->id)
            ->where('is_seen', false)
            ->count();

        $totalChatGc = GroupChatContent::where('user_id', '!=', $user->id)
            ->whereHas('groupChat', function ($query) use ($user) {
                $query->whereHas('groupChatMembers', function ($subQuery) use ($user) {
                    $subQuery->where('user_id', $user->id);
                });
            })
            ->whereDoesntHave('groupChatSeenBies', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->count();

        $allUsers = User::where('id', '!=', auth()->user()->id)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search_member . '%');
            })
            ->orderBy('name', 'asc')
            ->get();

        $groupChats = GroupChat::whereHas('groupChatMembers', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->withCount(['groupChatContents as unseen_count' => function ($query) use ($user) {
                $query->where('user_id', '!=', $user->id)
                    ->whereDoesntHave('groupChatSeenBies', function ($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id);
                    });
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        $searchedGc = GroupChat::where('group_chat_name', 'like', '%' . $this->searched_gc . '%')
            ->whereHas('groupChatMembers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->orderBy('created_at', 'desc')
            ->get();

        $this->usersCount = User::count();

        return compact(
            'users',
            'searched',
            'totalChats',
            'allUsers',
            'groupChats',
            'searchedGc',
            'totalChatGc'
        );
    }

    public function peopleClick()
    {
        $this->people = true;
        $this->groupChat = false;
    }
    public function groupChatClick()
    {
        $this->groupChat = true;
        $this->people = false;
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

    public function createGc()
    {
        $this->validate([
            'group_chat_name'           =>              ['required', 'max:30', 'min:4']
        ]);

        $userId = auth()->user()->id;

        if (!in_array($userId, $this->member)) {
            $this->member[] = $userId;
        }

        $token = Str::random(50);

        $group_chat = GroupChat::create([
            'group_chat_name'               =>              $this->group_chat_name,
            'group_chat_token'              =>              $token
        ]);

        foreach ($this->member as $memberId) {
            $isAdmin = ($memberId == $userId);

            $toAttact[$memberId] = ['is_admin' => $isAdmin];
        }

        $group_chat->groupChatMembers()->attach($toAttact);

        $this->dispatch('toastr', [
            'type'          =>          'success',
            'message'       =>          $group_chat->group_chat_name . ' group chat created successfully',
        ]);

        event(new GroupChatCreated($group_chat));

        $this->reset(['group_chat_name', 'member', 'search_member']);

        $this->dispatch('closeModal');
    }

    public function render()
    {
        return view('livewire.chats.index', $this->chats());
    }
}
