<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Info; // Maps to info_table
use App\Models\Service; // Maps to service_table

#[Layout('layouts.usermaster')]
class Appointment extends Component
{
    // Tracking the wizard
    public $currentStep = 1;
    
    // Storing the user's selections
    public $selectedPet = null;
    public $selectedService = null;

    public function nextStep()
    {
        // Simple validation before moving forward
        if ($this->currentStep == 1 && !$this->selectedPet) {
            $this->addError('selection', 'Please select a pet to continue.');
            return;
        }
        
        if ($this->currentStep == 2 && !$this->selectedService) {
            $this->addError('selection', 'Please select a service to continue.');
            return;
        }

        $this->resetErrorBag();
        $this->currentStep++;
    }

    public function previousStep()
    {
        $this->resetErrorBag();
        $this->currentStep--;
    }

    public function render()
    {
        // Fetch data based on the logged-in user and active services
        $pets = Info::where('userID', Auth::id())->get();
        $services = Service::where('isactive', true)->get();

        return view('livewire.user.appointment', [
            'pets' => $pets,
            'services' => $services
        ]);
    }
}