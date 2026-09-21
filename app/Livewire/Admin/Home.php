<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Home extends Component
{
    #[Layout('layouts.adminmaster')]
    public function render()
    {

        return view('livewire.admin.home');

    }
}
