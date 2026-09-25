<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Service; 

#[Layout('layouts.usermaster')]
class Services extends Component
{
    public function render()
    {
        $services = Service::orderBy('servicename', 'asc')->get();

        return view('livewire.user.services', [
            'services' => $services
        ]);
    }
}