<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Info;
use Carbon\Carbon;

#[Layout('layouts.usermaster')]
class Home extends Component
{
// --- S ---
    public $isModalOpen = false;
    public $modalTitle = '';
    public $modalAppointments = [];

    // --- Reschedule variables ---
    public $isRescheduling = false;
    public $rescheduleApptId = null;
    public $selectedDate = null;
    public $selectedTime = null;




    public function cancelAppointment($id)
    {
        // Ensures the user can only cancel their own appointments
        Appointment::where('appointmentID', $id)
            ->where('userID', Auth::id())
            ->update(['status' => 'Cancelled']);

        session()->flash('success_cancel', 'Appointment has been successfully cancelled.');
    }

    public function openUpcomingModal()
    {
        $this->modalTitle = 'Approved Appointments';
        $this->modalAppointments = $this->fetchModalData(['Approved']);
        $this->isModalOpen = true;
    }

    public function openCompletedModal()
    {
        $this->modalTitle = 'Completed Appointments';
        $this->modalAppointments = $this->fetchModalData(['Completed']);
        $this->isModalOpen = true;
    }

    public function openHistoryModal()
    {
        $this->modalTitle = 'Total Appointment History';
        // Passing an empty array bypasses the status filter to fetch ALL records
        $this->modalAppointments = $this->fetchModalData([]);
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->modalAppointments = [];
    }

    // get the correct data from the reused card 
    private function fetchModalData($statuses = [])
    {
        $query = Appointment::select(
                'appointment_table.*',
                'info_table.petname as pet_name',
                'users_table.fullname as patient_name',
                'service_table.servicename as service_name',
                'service_table.price as service_price'
            )
            ->leftJoin('info_table', 'appointment_table.infoID', '=', 'info_table.infoID')
            ->leftJoin('users_table', 'appointment_table.userID', '=', 'users_table.userID')
            ->leftJoin('service_table', 'appointment_table.serviceID', '=', 'service_table.serviceID')
            ->where('appointment_table.userID', Auth::id());

        if (!empty($statuses)) {
            $query->whereIn('appointment_table.status', $statuses);
        }

        return $query->orderBy('appointment_table.created_at', 'desc')->get();
    }

    


    //reschedule function
    public function openRescheduleModal($id)
    {
        $this->rescheduleApptId = $id;
        $this->isRescheduling = true;
        $this->monthOffset = 0;
        $this->selectedDate = null;
        $this->selectedTime = null;
        $this->resetErrorBag();
    }

    public function closeRescheduleModal()
    {
        $this->isRescheduling = false;
        $this->rescheduleApptId = null;
    }

    public function confirmReschedule()
    {
        if (!$this->selectedDate || !$this->selectedTime) {
            $this->addError('reschedule', 'Please select both a new date and time.');
            return;
        }

        Appointment::where('appointmentID', $this->rescheduleApptId)
            ->where('userID', Auth::id())
            ->update([
                'appointmentdate' => $this->selectedDate,
                'appointmenttime' => Carbon::parse($this->selectedTime)->format('H:i:s'),
                'status' => 'Pending' // Reverts to Pending so staff can verify the new slot
            ]);

        $this->closeRescheduleModal();
        session()->flash('success_cancel', 'Appointment successfully rescheduled and is now pending approval.');
    }

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

    public function refreshAppointments()
    {
      
    }

    public function getUnavailableDates()
    {
        return Appointment::select('appointmentdate')
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
        
        $bookedRecords = Appointment::where('appointmentdate', $this->selectedDate)
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
        $userId = Auth::id();

        // get all the upcoming appointments
        $allUpcoming = Appointment::select(
                'appointment_table.*',
                'info_table.petname as pet_name',
                'info_table.petimage as pet_image',
                'service_table.servicename as service_name'
            )
            ->leftJoin('info_table', 'appointment_table.infoID', '=', 'info_table.infoID')
            ->leftJoin('service_table', 'appointment_table.serviceID', '=', 'service_table.serviceID')
            ->where('appointment_table.userID', $userId)
            ->whereIn('appointment_table.status', ['Pending', 'Approved'])
            ->orderBy('appointment_table.appointmentdate', 'asc')
            ->orderBy('appointment_table.appointmenttime', 'asc')
            ->get();

        $nearestAppointment = $allUpcoming->first(); 
        $otherAppointments = $allUpcoming->skip(1);  

// Calendar variables
        $now = Carbon::now();
        $startDate = $now->copy()->addDay()->startOfDay(); // Tomorrow
        
        $availableDates = [];
        for ($i = 0; $i < 3; $i++) {
            $availableDates[] = $startDate->copy()->addDays($i)->format('Y-m-d');
        }


        $startOfMonth = $startDate->copy()->startOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;
        $firstDayOfWeek = $startOfMonth->dayOfWeek;
        $currentMonthName = $startOfMonth->format('F Y');
        $currentYearMonth = $startOfMonth->format('Y-m');

        $activePetsCount = Info::where('userID', $userId)
            ->where('is_archived', false)
            ->count();

        $upcomingVisitsCount = $allUpcoming->count();

        $completedVisitsCount = Appointment::where('userID', $userId)
            ->where('status', 'Completed')
            ->count();

        $totalAppointmentsCount = Appointment::where('userID', $userId)->count();

        return view('livewire.user.home', [
            'nearestAppointment' => $nearestAppointment,
            'otherAppointments' => $otherAppointments,
            'activePetsCount' => $activePetsCount,
            'upcomingVisitsCount' => $upcomingVisitsCount,
            'completedVisitsCount' => $completedVisitsCount,
            'totalAppointmentsCount' => $totalAppointmentsCount,
 
            'daysInMonth' => $daysInMonth,
            'firstDayOfWeek' => $firstDayOfWeek,
            'currentMonthName' => $currentMonthName,
            'currentYearMonth' => $currentYearMonth,
            'today' => $startDate,
            'availableDates' => $availableDates,
            'unavailableDates' => $this->getUnavailableDates(),
            'timeSlots' => $this->getAvailableTimeSlots(),
        ]);
    }
}