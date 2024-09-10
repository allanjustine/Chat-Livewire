<?php

namespace App\Livewire\Layouts;

use App\Events\MessageSent;
use App\Events\UserLoggedOut;
use App\Models\Chat;
use Livewire\Attributes\On;
use Livewire\Component;

class Navbar extends Component
{

    public $totalChats = 0;

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

    #[On('echo:sendMessage,MessageSent')]
    #[On('echo:isSeen,IsSeen')]
    public function chatsCount()
    {
        if (auth()->check()) {
            $this->totalChats = Chat::where('receiver_id', auth()->user()->id)->where('is_seen', false)->count();
        }
    }

    public function mount()
    {
        $this->chatsCount();
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

    public function markAllAsRead()
    {
        $user = auth()->user();

        $user->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.layouts.navbar');
    }
}
