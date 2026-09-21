<?php

namespace App\Livewire\Assistant;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Home extends Component
{
    #[Layout('layouts.assistantmaster')]
    public function render()
    {

        return view('livewire.assistant.home');

    }
}
