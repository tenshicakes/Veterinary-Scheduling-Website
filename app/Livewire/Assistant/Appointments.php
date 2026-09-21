<?php

namespace App\Livewire\Assistant;

use Livewire\Component;
use Livewire\WithPagination; // I added this to prevent lag when history gets huge
use Livewire\Attributes\Layout;
use App\Models\Appointment as AppointmentModel;
use Carbon\Carbon;

#[Layout('layouts.assistantmaster')]
class Appointments extends Component
{
    use WithPagination; // Allows us to use ->paginate(10) safely

    public $search = '';
    public $activeTab = 'Pending'; // Pending as the default
    public $sortFilter = 'newest_request';

    // --- RESCHEDULE MODAL VARIABLES ---
    public $isRescheduling = false;
    public $rescheduleApptId = null;
    public $reschedulePatientName = '';
    
    // --- SHARED CALENDAR VARIABLES ---
    public $monthOffset = 0;
    public $selectedDate = null;
    public $selectedTime = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSortFilter()
    {
        $this->resetPage();
    }

    // Switches tabs and resets pagination
    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // added a single, reusable method to handle all status changes without writing 5 different functions
    public function updateStatus($id, $newStatus)
    {
        AppointmentModel::where('appointmentID', $id)->update(['status' => $newStatus]);
    }

    // --- RESCHEDULE LOGIC ---

    // Opens the modal and saves which appointment we are moving
    public function openRescheduleModal($id, $patientName)
    {
        $this->rescheduleApptId = $id;
        $this->reschedulePatientName = $patientName;
        $this->isRescheduling = true;
        
        // Reset the calendar selections for a fresh start
        $this->monthOffset = 0;
        $this->selectedDate = null;
        $this->selectedTime = null;
        $this->resetErrorBag();
    }

    // Closes the modal
    public function closeRescheduleModal()
    {
        $this->isRescheduling = false;
        $this->rescheduleApptId = null;
    }

    // Saves the new date and time to the database
    public function confirmReschedule()
    {
        if (!$this->selectedDate || !$this->selectedTime) {
            $this->addError('reschedule', 'Please select both a new date and time.');
            return;
        }

        AppointmentModel::where('appointmentID', $this->rescheduleApptId)->update([
            'appointmentdate' => $this->selectedDate,
            'appointmenttime' => Carbon::parse($this->selectedTime)->format('H:i:s'),
            'status' => 'Approved' // Automatically approve it since the clinic staff handled it
        ]);

        $this->closeRescheduleModal();
        session()->flash('success', 'Appointment successfully rescheduled!');
    }

    // --- SHARED CALENDAR METHODS (Copied from User side) ---

    public function nextMonth()
    {
        $this->monthOffset++;
        $this->selectedDate = null;
        $this->selectedTime = null;
    }

    public function previousMonth()
    {
        if ($this->monthOffset > 0) {
            $this->monthOffset--;
            $this->selectedDate = null;
            $this->selectedTime = null;
        }
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->selectedTime = null;
        $this->resetErrorBag('reschedule');
    }

    public function getUnavailableDates()
    {
        return AppointmentModel::select('appointmentdate')
            ->whereIn('status', ['Pending', 'Approved'])
            ->groupBy('appointmentdate')
            ->havingRaw('COUNT(*) >= 9')
            ->pluck('appointmentdate')
            ->toArray();
    }

    public function getAvailableTimeSlots()
    {
        if (!$this->selectedDate) return [];

        $allSlots = ['09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '01:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM'];
        
        $bookedRecords = AppointmentModel::where('appointmentdate', $this->selectedDate)
            ->whereIn('status', ['Pending', 'Approved'])
            ->pluck('appointmenttime')
            ->toArray();

        $bookedSlots = array_map(function($time) {
            return Carbon::parse($time)->format('h:i A');
        }, $bookedRecords);

        $availableSlots = [];
        $now = Carbon::now();
        $isToday = $this->selectedDate === $now->format('Y-m-d');

        foreach ($allSlots as $slot) {
            if (in_array($slot, $bookedSlots)) continue;
            
            if ($isToday) {
                $slotExpirationTime = Carbon::parse($this->selectedDate . ' ' . $slot)->addMinutes(30);
                if ($now->isAfter($slotExpirationTime)) continue;
            }
            $availableSlots[] = $slot;
        }

        return $availableSlots;
    }

    public function render()
    {
        // 1. I added the 10-Minute Auto-Cancel logic here so it cleans up the DB before loading the cards
        AppointmentModel::where('status', 'Pending')
            ->where('created_at', '<', Carbon::now()->subMinutes(10))
            ->update(['status' => 'Cancelled']);

        // 2. Figure out which statuses to load based on the clicked tab
        $statuses = [];
        if ($this->activeTab == 'Pending') $statuses = ['Pending'];
        if ($this->activeTab == 'Upcoming') $statuses = ['Approved'];
        if ($this->activeTab == 'History') $statuses = ['Completed', 'Cancelled', 'No-Show', 'Rejected'];

        // 3. I used standard SQL Joins here to pull the Patient Name, Pet Name, and Service Price directly.
        // This avoids complex Eloquent Model Shenanigans and keeps everything flat and easy to read.
        // 3. I used standard SQL Joins here to pull the Patient Name, Pet Name, and Service Price directly.
        $query = AppointmentModel::select(
                'appointment_table.*',
                'users_table.fullname as patient_name',
                'info_table.petname as pet_name',
                'service_table.servicename as service_name',
                'service_table.price as service_price'
            )
            ->leftJoin('users_table', 'appointment_table.userID', '=', 'users_table.userID')
            ->leftJoin('info_table', 'appointment_table.infoID', '=', 'info_table.infoID')
            ->leftJoin('service_table', 'appointment_table.serviceID', '=', 'service_table.serviceID')
            ->whereIn('appointment_table.status', $statuses);

        // 4. If they typed something in the search bar, filter the results in real-time
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('users_table.fullname', 'like', '%' . $this->search . '%')
                  ->orWhere('appointment_table.notes', 'like', '%' . $this->search . '%');
            });
        }

        // 5. I added this filtering block to apply the sorting based on the dropdown selection
        if ($this->sortFilter == 'newest_request') {
            $query->orderBy('appointment_table.created_at', 'desc'); // Most recent bookings first
        } elseif ($this->sortFilter == 'oldest_request') {
            $query->orderBy('appointment_table.created_at', 'asc'); // Oldest bookings first
        } elseif ($this->sortFilter == 'nearest_appt') {
            $query->orderBy('appointment_table.appointmentdate', 'asc') // Nearest clinic visit
                  ->orderBy('appointment_table.appointmenttime', 'asc');
        } elseif ($this->sortFilter == 'farthest_appt') {
            $query->orderBy('appointment_table.appointmentdate', 'desc') // Furthest clinic visit
                  ->orderBy('appointment_table.appointmenttime', 'desc');
        }

        $appointments = $query->paginate(10);

        // --- SHARED CALENDAR RENDER MATH ---
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->addMonths($this->monthOffset)->startOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;
        $firstDayOfWeek = $startOfMonth->dayOfWeek;
        $currentMonthName = $startOfMonth->format('F Y');
        $currentYearMonth = $startOfMonth->format('Y-m');

        return view('livewire.assistant.appointments', [
            'appointments' => $appointments,
            'daysInMonth' => $daysInMonth,
            'firstDayOfWeek' => $firstDayOfWeek,
            'currentMonthName' => $currentMonthName,
            'currentYearMonth' => $currentYearMonth,
            'today' => $today,
            'unavailableDates' => $this->getUnavailableDates(),
            'timeSlots' => $this->getAvailableTimeSlots(),
        ]);
    }
}