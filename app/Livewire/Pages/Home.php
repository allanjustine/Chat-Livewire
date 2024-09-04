<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class Home extends Component
{
    #[Title('Home')]

    public function render()
    {
        return view('livewire.pages.home');
    }
}
