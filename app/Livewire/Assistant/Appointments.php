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

    // I added a single, reusable method to handle all status changes without writing 5 different functions
    public function updateStatus($id, $newStatus)
    {
        AppointmentModel::where('appointmentID', $id)->update(['status' => $newStatus]);
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

        return view('livewire.assistant.appointments', [
            'appointments' => $appointments
        ]);
    }
}