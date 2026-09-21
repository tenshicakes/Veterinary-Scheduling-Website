<?php

namespace App\Livewire\User;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Home extends Component
{
    #[Layout('layouts.usermaster')]
    public function render()
    {

        return view('livewire.user.home');

    }
}
