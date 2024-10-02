<?php

namespace App\Livewire\Layouts;

use App\Events\MessageSent;
use App\Events\UserLoggedOut;
use App\Models\Chat;
use App\Models\GroupChatContent;
use Livewire\Attributes\On;
use Livewire\Component;

class Navbar extends Component
{

    public $totalChats = 0;
    public $notifications = [];
    public $unreadNotificationsCount;
    public $authUser;
    public $pageTitle;
    public $authId = '';

    public function confirmLogout()
    {
        $this->dispatch('swal', [
            'title'       =>          'Logout',
            'text'        =>          'Are you sure you want to logout? You will redirect to login page.',
            'icon'        =>          'info'
        ]);
    }

    #[On(['onLogout'])]
    public function logout()
    {
        $user = auth()->user();
        if (auth()->check()) {
            $user->update(['status' => 'offline']);
            auth()->logout();
        }

        $this->redirect('/login', navigate: true);
    }

    #[On('userLogout')]
    public function userLogout()
    {
        $user = auth()->user();

        if (auth()->check()) {
            $user->update(['status' => 'offline']);
        }
    }

    #[On(['userAway'])]
    public function away()
    {
        $user = auth()->user();
        if (auth()->check()) {
            if ($user->status !== 'away' && $user->status !== 'offline') {
                $user->update(['status' => 'away']);
            }
        }
    }

    public function updateNotificationAndChatCounts()
    {
        $this->authUser = auth()->user();

        $this->unreadNotificationsCount = $this->authUser ? $this->authUser->unreadNotifications()->count() : 0;
        $this->notifications = $this->authUser ? $this->authUser->notifications : [];

        $this->pageTitle = ($this->totalChats + $this->unreadNotificationsCount) > 0
        ? ($this->totalChats + $this->unreadNotificationsCount)
        : '';

        if (($this->totalChats + $this->unreadNotificationsCount) >= 0) {

            $this->dispatch('updateTitle', [
                'title' => $this->pageTitle
            ]);
        }
    }

    #[On('echo:announcementPost,NotificationPost')]
    #[On('echo:sendMessage.{authId},MessageSent')]
    #[On('echo:isSeen.{authId},IsSeen')]
    #[On('echo:gcIsSeen.{authId},GroupChatIsSeen')]
    #[On('echo:groupChatMessage.{authId},GroupChatMessageSent')]
    public function chatsCount()
    {
        $user = auth()->user();

        if(auth()->check()) {
            $this->authId = $user->id;
        }

        if (auth()->check()) {
            $directChat = Chat::where('receiver_id', $user->id)
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

            $this->totalChats = ($directChat + $totalChatGc);

            $this->updateNotificationAndChatCounts();
        }
    }

    #[On(['userOnline'])]
    public function online()
    {
        $user = auth()->user();
        if (auth()->check()) {
            if ($user->status !== 'online') {
                $user->update(['status' => 'online']);
            }
        }
    }

    public function markAsRead($notificationId)
    {
        $user = auth()->user();
        $notification = $user->notifications->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAsUnRead($notificationId)
    {
        $user = auth()->user();
        $notification = $user->notifications->find($notificationId);

        if ($notification) {
            $notification->update(['read_at' => null]);
        }
    }

    public function markAllAsRead()
    {
        $user = auth()->user();

        $user->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.layouts.navbar', [
            $this->chatsCount()
        ]);
    }
}
