<?php

namespace App\Livewire\Superadmin;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Profile extends Component
{
    #[Layout('layouts.supermaster')]
    public function render()
    {

        return view('livewire.superadmin.profile');

    }
}
