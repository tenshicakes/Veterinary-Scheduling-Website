<?php

namespace App\Livewire\Superadmin;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Home extends Component
{
    #[Layout('layouts.supermaster')]
    public function render()
    {
        
        return view('livewire.superadmin.home');
            
    }
}