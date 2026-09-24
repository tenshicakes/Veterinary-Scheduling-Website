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
        // Fetch all services to display on the pricing page
        $services = Service::orderBy('servicename', 'asc')->get();

        return view('livewire.user.services', [
            'services' => $services
        ]);
    }
}