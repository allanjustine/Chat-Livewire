<?php

namespace App\Livewire\Layouts;

use Livewire\Attributes\On;
use Livewire\Component;

class Base extends Component
{
    public function render()
    {
        return view('livewire.layouts.base');
    }
}
