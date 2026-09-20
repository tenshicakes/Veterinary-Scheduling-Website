<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Info; // Maps to info_table
use App\Models\Service; // Maps to service_table
use App\Models\Appointment as AppointmentModel;
use Carbon\Carbon; // Laravel's built-in date tool

#[Layout('layouts.usermaster')]
class Appointment extends Component
{
    // Tracking the wizard
    public $currentStep = 1;
    
    // Storing the user's selections
    public $selectedPet = null;
    public $selectedService = null;

    public $selectedDate = null;
    public $selectedTime = null;

    public $referenceNumber = '';
    public $notes = '';

    // This method is ready to be connected to your Admin/Assistant setup later
    public function getUnavailableDates()
    {
        // For now, we hardcode the 20th of the current month to be grayed out, 
        // as well as any dates in the past.
        return [
            Carbon::now()->format('Y-m') . '-20'
        ];
    }

    // This method is ready to fetch available slots from the database later
    public function getAvailableTimeSlots()
    {
        // If no date is clicked yet, return nothing
        if (!$this->selectedDate) return [];

        // For now, returning dummy slots that the Admin would theoretically set
        return ['09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '01:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM'];
    }

    // Custom method to select a date so we can reset the time slot simultaneously
    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->selectedTime = null; // Clear time if they change their mind on the date
        $this->resetErrorBag('selection');
    }
    

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

        // Step 3 Validation
        if ($this->currentStep == 3 && (!$this->selectedDate || !$this->selectedTime)) {
            $this->addError('selection', 'Please select both an available date and time.');
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

    public function confirmAppointment()
    {
        // 1. Validate the payment and notes
        $this->validate([
            'referenceNumber' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ], [
            'referenceNumber.required' => 'Please provide the payment reference number to proceed.'
        ]);

        // 2. Final security check
        if (!$this->selectedPet || !$this->selectedService || !$this->selectedDate || !$this->selectedTime) {
            $this->addError('selection', 'Missing information. Please go back and check your selections.');
            return;
        }

        // 3. Combine Reference Number and Notes into one string for the 'notes' column
        $combinedNotes = "Payment Ref: " . $this->referenceNumber;
        if (!empty($this->notes)) {
            $combinedNotes .= " | Extra Notes: " . $this->notes;
        }

        // 4. Save to the appointment_table
        AppointmentModel::create([
            'userID' => Auth::id(),
            'infoID' => $this->selectedPet,
            'serviceID' => $this->selectedService,
            'appointmentdate' => $this->selectedDate,
            'appointmenttime' => Carbon::parse($this->selectedTime)->format('H:i:s'),
            'status' => 'Pending', 
            'notes' => $combinedNotes
        ]);

        // 5. Clear form and redirect
        session()->flash('success', 'Your appointment request is pending payment verification!');
        return redirect()->route('user.home');
    }

    public function render()
    {
        // Fetch data based on the logged-in user and active services
        $pets = Info::where('userID', Auth::id())->get();
        $services = Service::where('isactive', true)->get();

        // Calendar Logic (Current Month Only)
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;
        $firstDayOfWeek = $startOfMonth->dayOfWeek; // 0 (Sun) to 6 (Sat)
        $currentMonthName = $startOfMonth->format('F Y');

        return view('livewire.user.appointment', [
            'pets' => $pets,
            'services' => $services,
            'daysInMonth' => $daysInMonth,
            'firstDayOfWeek' => $firstDayOfWeek,
            'currentMonthName' => $currentMonthName,
            'today' => $today,
            'unavailableDates' => $this->getUnavailableDates(),
            'timeSlots' => $this->getAvailableTimeSlots(),
        ]);
    }
}