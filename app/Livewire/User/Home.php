<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Info;

#[Layout('layouts.usermaster')]
class Home extends Component
{
// --- MODAL VARIABLES ---
    public $isModalOpen = false;
    public $modalTitle = '';
    public $modalAppointments = [];





    public function cancelAppointment($id)
    {
        // Ensures the user can only cancel their own appointments
        Appointment::where('appointmentID', $id)
            ->where('userID', Auth::id())
            ->update(['status' => 'Cancelled']);

        session()->flash('success_cancel', 'Appointment has been successfully cancelled.');
    }

    // --- MODAL LOGIC ---
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

    // Helper method to fetch the correct data for the shared card
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

    public function render()
    {
        $userId = Auth::id();

        // 1. Fetch all upcoming appointments (Sorted by nearest date/time first)
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

        // Split the nearest appointment from the rest
        $nearestAppointment = $allUpcoming->first(); 
        $otherAppointments = $allUpcoming->skip(1);  

        // 2. Calculate the Redesigned Counters
        $activePetsCount = Info::where('userID', $userId)
            ->where('is_archived', false)
            ->count();

        $upcomingVisitsCount = $allUpcoming->count();

        $completedVisitsCount = Appointment::where('userID', $userId)
            ->where('status', 'Completed')
            ->count();

        // Total history for this user (All statuses)
        $totalAppointmentsCount = Appointment::where('userID', $userId)->count();

        return view('livewire.user.home', [
            'nearestAppointment' => $nearestAppointment,
            'otherAppointments' => $otherAppointments,
            'activePetsCount' => Info::where('userID', $userId)->where('is_archived', false)->count(),
            'upcomingVisitsCount' => $allUpcoming->count(),
            'completedVisitsCount' => Appointment::where('userID', $userId)->where('status', 'Completed')->count(),
            'totalAppointmentsCount' => Appointment::where('userID', $userId)->count(),
        ]);
    }
}