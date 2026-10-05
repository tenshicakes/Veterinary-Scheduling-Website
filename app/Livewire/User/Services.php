<?php

namespace App\Livewire\User;

use App\Models\Service;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.usermaster')]
class Services extends Component
{
    public function render()
    {
        $services = Service::orderBy('servicename', 'asc')->get();

        return view('livewire.user.services', [
            'services' => $services,
        ]);
    }
}
