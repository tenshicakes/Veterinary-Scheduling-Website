<?php

namespace App\Livewire\User;

use App\Models\Appointment as AppointmentModel;
use App\Models\Info;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.usermaster')]
class Appointment extends Component
{
    public $currentStep = 1;
    public $selectedPet = null;
    public $selectedService = null;
    public $selectedDate = null;
    public $selectedTime = null;


    public $referenceNumber = '';

    public $notes = '';



    // --- SCHEDULING LOGIC ---

   
    public function getUnavailableDates()
    {
       
        $fullyBookedDates = AppointmentModel::select('appointmentdate')
            ->whereIn('status', ['Pending', 'Approved'])
            ->groupBy('appointmentdate')
            ->havingRaw('COUNT(*) >= 9')
            ->pluck('appointmentdate')
            ->toArray();

        return $fullyBookedDates;
    }

    public function getAvailableTimeSlots()
    {

        if (! $this->selectedDate) {
            return [];
        }

        // Clinic schedule 
        $allSlots = ['09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '01:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM'];

        $bookedRecords = AppointmentModel::where('appointmentdate', $this->selectedDate)
            ->whereIn('status', ['Pending', 'Approved'])
            ->pluck('appointmenttime')
            ->toArray();

        
        $bookedSlots = array_map(function ($time) {
            return Carbon::parse($time)->format('h:i A');
        }, $bookedRecords);

        $availableSlots = [];
        $now = Carbon::now();
        $isToday = $this->selectedDate === $now->format('Y-m-d');

        foreach ($allSlots as $slot) {

        
            if (in_array($slot, $bookedSlots)) {
                continue;
            }

         
            if ($isToday) {
                $slotExpirationTime = Carbon::parse($this->selectedDate.' '.$slot)->addMinutes(30);
                if ($now->isAfter($slotExpirationTime)) {
                    continue;
                }
            }

            $availableSlots[] = $slot;
        }

        return $availableSlots;
    }

  
    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->selectedTime = null;
        $this->resetErrorBag('selection'); 
    }






    public function nextStep()
    {
        // Step 1: Did they pick pet?
        if ($this->currentStep == 1 && ! $this->selectedPet) {
            $this->addError('selection', 'Please select a pet to continue.');

            return;
        }

        // Step 2 : Did they pick a service?
        if ($this->currentStep == 2 && ! $this->selectedService) {
            $this->addError('selection', 'Please select a service to continue.');

            return;
        }

        // Step 3: Did they pick both date and time?
        if ($this->currentStep == 3 && (! $this->selectedDate || ! $this->selectedTime)) {
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






    // --- FINAL SUBMISSION ---

    public function confirmAppointment()
    {
        $this->validate([
            'referenceNumber' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ], [
            'referenceNumber.required' => 'Please provide the payment reference number to proceed.',
        ]);

        
        if (! $this->selectedPet || ! $this->selectedService || ! $this->selectedDate || ! $this->selectedTime) {
            $this->addError('selection', 'Missing information. Please go back and check your selections.');

            return;
        }

        $combinedNotes = 'Payment Ref: '.$this->referenceNumber;
        if (! empty($this->notes)) {
            $combinedNotes .= ' | Extra Notes: '.$this->notes;
        }


        AppointmentModel::create([
            'userID' => Auth::id(),
            'infoID' => $this->selectedPet,
            'serviceID' => $this->selectedService,
            'appointmentdate' => $this->selectedDate,
            'appointmenttime' => Carbon::parse($this->selectedTime)->format('H:i:s'), // Format to 24hr for MySQL
            'status' => 'Pending',
            'notes' => $combinedNotes,
        ]);

       
        session()->flash('success', 'Your appointment request is pending payment verification!');

        return redirect()->route('user.home');
    }







    // --- PAGE DISPLAY ---

    public function render()
    {
   
        $pets = Info::where('userID', Auth::id())->get();
        $services = Service::where('isactive', true)->get();
        $now = Carbon::now();
        $startDate = $now->copy()->addDay()->startOfDay(); // Tomorrow
        $availableDates = [];
        for ($i = 0; $i < 3; $i++) {
            $availableDates[] = $startDate->copy()->addDays($i)->format('Y-m-d');
        }

        $startOfMonth = $startDate->copy()->startOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;
        $firstDayOfWeek = $startOfMonth->dayOfWeek; // 0 = Sun, 6 = Sat
        $currentMonthName = $startOfMonth->format('F Y');
        $currentYearMonth = $startOfMonth->format('Y-m');

        if (! $this->selectedDate || ! in_array($this->selectedDate, $availableDates)) {
            $this->selectedDate = $availableDates[0];
        }

 


        
        return view('livewire.user.appointment', [
            'pets' => $pets,
            'services' => $services,
            'daysInMonth' => $daysInMonth,
            'firstDayOfWeek' => $firstDayOfWeek,
            'currentMonthName' => $currentMonthName,
            'currentYearMonth' => $currentYearMonth,
            'today' => $startDate, // Use start date for calendar logic
            'availableDates' => $availableDates, // Pass the 3 available dates
            'unavailableDates' => $this->getUnavailableDates(),
            'timeSlots' => $this->getAvailableTimeSlots(),
        ]);
    }
}