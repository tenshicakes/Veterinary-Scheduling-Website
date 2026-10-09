<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Usermanage extends Component
{
    #[Layout('layouts.adminmaster')]
    public function render()
    {

        return view('livewire.admin.usermanage');

    }
}
