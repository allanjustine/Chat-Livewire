<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Title;
use Livewire\Component;

class Landing extends Component
{
    #[Title('Welcome to Chat')]

    public function render()
    {
        if (auth()->check()) {
            $this->redirect('/home', navigate: true);
        }

        return view('livewire.pages.landing');
    }
}
