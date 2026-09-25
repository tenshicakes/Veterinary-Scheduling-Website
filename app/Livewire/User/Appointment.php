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
    // Tracking which step of the 4-step wizard the user is currently on
    public $currentStep = 1;

    // Variables to hold whatever the user clicks so we can save it later
    public $selectedPet = null;

    public $selectedService = null;

    public $selectedDate = null;

    public $selectedTime = null;

    // Step 4 inputs for payment and extra instructions
    public $referenceNumber = '';

    public $notes = '';

    // --- SCHEDULING LOGIC ---

    // This method is ready to be connected to the Admin/Assistant setup later to block out holidays
    public function getUnavailableDates()
    {
        // Groups appointments by date and checks if all 9 slots are taken by Pending or Approved status
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
        // 1. If no date is clicked yet on the calendar, show nothing
        if (! $this->selectedDate) {
            return [];
        }

        // 2. The baseline clinic schedule
        $allSlots = ['09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '01:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM'];

        // 3. Query the database for slots already taken on this specific date
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

        // 4. Loop through the baseline schedule and filter it
        foreach ($allSlots as $slot) {

            // If the slot is already in the booked list from the database, skip it!
            if (in_array($slot, $bookedSlots)) {
                continue;
            }

            // Check the 30-minute expiration rule for today's current time
            if ($isToday) {
                $slotExpirationTime = Carbon::parse($this->selectedDate.' '.$slot)->addMinutes(30);
                if ($now->isAfter($slotExpirationTime)) {
                    continue;
                }
            }

            // If it passed all checks, add it to the available buttons
            $availableSlots[] = $slot;
        }

        return $availableSlots;
    }

    // When a user clicks a day on the calendar, save it and reset the time slot just in case
    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->selectedTime = null;
        $this->resetErrorBag('selection'); // Clear any red error text
    }

    // --- WIZARD NAVIGATION ---

    // Validates the current step before letting them go to the next one
    public function nextStep()
    {
        // Step 1 check: Did they pick a pet?
        if ($this->currentStep == 1 && ! $this->selectedPet) {
            $this->addError('selection', 'Please select a pet to continue.');

            return;
        }

        // Step 2 check: Did they pick a service?
        if ($this->currentStep == 2 && ! $this->selectedService) {
            $this->addError('selection', 'Please select a service to continue.');

            return;
        }

        // Step 3 check: Did they pick both date and time?
        if ($this->currentStep == 3 && (! $this->selectedDate || ! $this->selectedTime)) {
            $this->addError('selection', 'Please select both an available date and time.');

            return;
        }

        // If everything is good, clear errors and go to next page
        $this->resetErrorBag();
        $this->currentStep++;
    }

    // Goes back one step and clears any lingering error messages
    public function previousStep()
    {
        $this->resetErrorBag();
        $this->currentStep--;
    }

    // --- FINAL SUBMISSION ---

    // Runs when they click the final confirm button on Step 4
    public function confirmAppointment()
    {
        // 1. Make sure they typed in the Instapay reference number
        $this->validate([
            'referenceNumber' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ], [
            'referenceNumber.required' => 'Please provide the payment reference number to proceed.',
        ]);

        // 2. Just a final backup check so it doesn't crash the database if something is missing
        if (! $this->selectedPet || ! $this->selectedService || ! $this->selectedDate || ! $this->selectedTime) {
            $this->addError('selection', 'Missing information. Please go back and check your selections.');

            return;
        }

        // 3. Combine the Ref Number and extra notes into one string so it fits in the single 'notes' column
        $combinedNotes = 'Payment Ref: '.$this->referenceNumber;
        if (! empty($this->notes)) {
            $combinedNotes .= ' | Extra Notes: '.$this->notes;
        }

        // 4. Save everything directly to the appointment_table in MySQL
        AppointmentModel::create([
            'userID' => Auth::id(),
            'infoID' => $this->selectedPet,
            'serviceID' => $this->selectedService,
            'appointmentdate' => $this->selectedDate,
            'appointmenttime' => Carbon::parse($this->selectedTime)->format('H:i:s'), // Format to 24hr for MySQL
            'status' => 'Pending',
            'notes' => $combinedNotes,
        ]);

        // 5. Done! Clear the form and send them back to their dashboard with a green success message
        session()->flash('success', 'Your appointment request is pending payment verification!');

        return redirect()->route('user.home');
    }

    // --- PAGE RENDER ---

    // This runs every time the page loads or a button is clicked
    public function render()
    {
        // Grab the logged-in user's pets and the active clinic services from the DB
        $pets = Info::where('userID', Auth::id())->get();
        $services = Service::where('isactive', true)->get();

        // Calculate available booking dates: tomorrow + 2 more days (3 days total)
        $now = Carbon::now();
        $startDate = $now->copy()->addDay()->startOfDay(); // Tomorrow
        
        // Generate array of 3 available dates (tomorrow, +1, +2)
        $availableDates = [];
        for ($i = 0; $i < 3; $i++) {
            $availableDates[] = $startDate->copy()->addDays($i)->format('Y-m-d');
        }

        // Set up calendar for the month containing the first available date
        $startOfMonth = $startDate->copy()->startOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;
        $firstDayOfWeek = $startOfMonth->dayOfWeek; // 0 = Sun, 6 = Sat
        $currentMonthName = $startOfMonth->format('F Y');
        $currentYearMonth = $startOfMonth->format('Y-m');

        // Auto-set selected date to tomorrow ONLY if not already selected
        if (! $this->selectedDate || ! in_array($this->selectedDate, $availableDates)) {
            $this->selectedDate = $availableDates[0];
        }

        // Send all these variables over to the appointment.blade.php view
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