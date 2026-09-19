<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Home extends Component
{
    #[Layout('layouts.adminmaster')]
    public function render()
    {
        
        return view('livewire.admin.home');
             
    }
}