<?php

namespace App\Livewire\Pages;

use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\URL;

class ProfileInfo extends Component
{

    #[Title('Profile')]

    public $previous;
    public $profileData;

    public function mount($username)
    {
        $profileInfo = User::where('username', $username)->first();

        $this->previous = URL::previous();

        if (!$profileInfo) {
            $this->redirect($this->previous, navigate: true);
        }
        $this->profileData = $profileInfo;
    }

    public function render()
    {
        return view('livewire.pages.profile-info');
    }
}
