<?php

namespace App\Livewire\Assistant;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Home extends Component
{
    #[Layout('layouts.assistantmaster')]
    public function render()
    {
        
        return view('livewire.assistant.home');
             
    }
}