<?php

namespace App\Livewire\Admin;

use App\Models\Appointment as AppointmentModel;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.adminmaster')]
class Appointments extends Component
{
    use WithPagination;

    public $search = '';

    public $activeTab = 'Pending';

    public $sortFilter = 'newest_request';

    // --- RESCHEDULE  VARIABLES ---
    public $isRescheduling = false;

    public $rescheduleApptId = null;

    public $reschedulePatientName = '';

    // --- SHARED CALENDAR VARIABLE ---
    public $selectedDate = null;

    public $selectedTime = null;

    public $monthOffset = 0;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSortFilter()
    {
        $this->resetPage();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updateStatus($id, $newStatus)
    {
        AppointmentModel::where('appointmentID', $id)->update(['status' => $newStatus]);
    }

    // --- RESCHEDULE LOGIC ---

    public function openRescheduleModal($id, $patientName)
    {
        $this->rescheduleApptId = $id;
        $this->reschedulePatientName = $patientName;
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
        if (! $this->selectedDate || ! $this->selectedTime) {
            $this->addError('reschedule', 'Please select both a new date and time.');

            return;
        }

        AppointmentModel::where('appointmentID', $this->rescheduleApptId)->update([
            'appointmentdate' => $this->selectedDate,
            'appointmenttime' => Carbon::parse($this->selectedTime)->format('H:i:s'),
            'status' => 'Approved', // Automatically approve it since the clinic staff handled it
        ]);

        $this->closeRescheduleModal();
        session()->flash('success', 'Appointment successfully rescheduled!');
    }

    public function refreshAppointments()
    {
        //
    }

    // --- SHARED CALENDAR METHODS ---

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->selectedTime = null;
        $this->resetErrorBag('reschedule');
    }

    private function getOverrides()
    {
        if (\Illuminate\Support\Facades\Storage::exists('schedule_overrides.json')) {
            return json_decode(\Illuminate\Support\Facades\Storage::get('schedule_overrides.json'), true);
        }
        return ['closed_dates' => [], 'blocked_timeslots' => []];
    }

    public function getUnavailableDates($availableDates)
    {
        $overrides = $this->getOverrides();
        $adminClosedDates = $overrides['closed_dates'] ?? [];
        $fullyBookedDates = [];

        $globalOpen = $overrides['operating_hours']['open'] ?? '09:00';
        $globalClose = $overrides['operating_hours']['close'] ?? '17:00';

        foreach ($availableDates as $date) {
            $openTime = Carbon::parse($globalOpen);
            $closeTime = Carbon::parse($globalClose);
            $totalBaseSlots = 0;
            
            while ($openTime->lessThan($closeTime)) {
                $totalBaseSlots++;
                $openTime->addHour();
            }

           $adminBlockedCount = count($overrides['blocked_timeslots'][$date] ?? []);
            $maxCapacityForThisDay = $totalBaseSlots - $adminBlockedCount;

            $dbCount = AppointmentModel::where('appointmentdate', $date)
                ->whereIn('status', ['Pending', 'Approved'])
                ->count();

            if ($maxCapacityForThisDay > 0 && $dbCount >= $maxCapacityForThisDay) {
                $fullyBookedDates[] = $date;
            }
        }

        return array_unique(array_merge($fullyBookedDates, $adminClosedDates));
    }

    public function getAvailableTimeSlots()
    {
        if (! $this->selectedDate) {
            return [];
        }

        $overrides = $this->getOverrides();
        
        $openTime = Carbon::parse($overrides['operating_hours']['open'] ?? '09:00');
        $closeTime = Carbon::parse($overrides['operating_hours']['close'] ?? '17:00');
        
        $allSlots = [];
        while ($openTime->lessThan($closeTime)) {
            $allSlots[] = $openTime->format('h:i A');
            $openTime->addHour();
        }

        $bookedRecords = AppointmentModel::where('appointmentdate', $this->selectedDate)
            ->whereIn('status', ['Pending', 'Approved'])
            ->pluck('appointmenttime')
            ->toArray();

        $bookedSlots = array_map(function ($time) {
            return Carbon::parse($time)->format('h:i A');
        }, $bookedRecords);

        $adminBlockedSlots = $overrides['blocked_timeslots'][$this->selectedDate] ?? [];

        $availableSlots = [];

        foreach ($allSlots as $slot) {
            if (in_array($slot, $bookedSlots) || in_array($slot, $adminBlockedSlots)) {
                continue;
            }
            $availableSlots[] = $slot;
        }

        return $availableSlots;
    }

    public function render()
    {

        $statuses = [];
        if ($this->activeTab == 'Pending') {
            $statuses = ['Pending'];
        }
        if ($this->activeTab == 'Upcoming') {
            $statuses = ['Approved'];
        }
        if ($this->activeTab == 'History') {
            $statuses = ['Completed', 'Cancelled', 'No-Show', 'Rejected'];
        }

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

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('users_table.fullname', 'like', '%'.$this->search.'%')
                    ->orWhere('appointment_table.notes', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->sortFilter == 'newest_request') {
            $query->orderBy('appointment_table.created_at', 'desc');
        } elseif ($this->sortFilter == 'oldest_request') {
            $query->orderBy('appointment_table.created_at', 'asc');
        } elseif ($this->sortFilter == 'nearest_appt') {
            $query->orderBy('appointment_table.appointmentdate', 'asc')
                ->orderBy('appointment_table.appointmenttime', 'asc');
        } elseif ($this->sortFilter == 'farthest_appt') {
            $query->orderBy('appointment_table.appointmentdate', 'desc')
                ->orderBy('appointment_table.appointmenttime', 'desc');
        }

        $appointments = $query->paginate(10);

        // --- CALENDAR SETUP FOR RESCHEDULE ---
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

        return view('livewire.admin.appointments', [
            'appointments' => $appointments,
            'daysInMonth' => $daysInMonth,
            'firstDayOfWeek' => $firstDayOfWeek,
            'currentMonthName' => $currentMonthName,
            'currentYearMonth' => $currentYearMonth,
            'today' => $startDate,
            'availableDates' => $availableDates,
            'unavailableDates' => $this->getUnavailableDates($availableDates),
            'timeSlots' => $this->getAvailableTimeSlots(),
        ]);
    }
}
