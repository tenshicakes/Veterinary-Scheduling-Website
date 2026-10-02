<?php

namespace App\Livewire\Assistant;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Appointment;
use Carbon\Carbon;

#[Layout('layouts.assistantmaster')]
class Home extends Component
{
    // Modal variables
    public $isModalOpen = false;
    public $modalTitle = '';
    public $modalAppointments = [];

    // A single, reusable method to handle all status updates (Approve, Reject, Complete, No-Show)
    public function updateStatus($appointmentID, $newStatus)
    {
        Appointment::where('appointmentID', $appointmentID)->update([
            'status' => $newStatus
        ]);

        session()->flash('success_action', "Appointment successfully marked as {$newStatus}.");
    }

    // Modal methods
    public function openCompletedTodayModal()
    {
        $today = Carbon::today()->format('Y-m-d');
        $this->modalTitle = 'Completed Today';
        
        $this->modalAppointments = Appointment::select(
                'appointment_table.*',
                'users_table.fullname as patient_name',
                'info_table.petname as pet_name',
                'service_table.servicename as service_name',
                'service_table.price as service_price'
            )
            ->leftJoin('users_table', 'appointment_table.userID', '=', 'users_table.userID')
            ->leftJoin('info_table', 'appointment_table.infoID', '=', 'info_table.infoID')
            ->leftJoin('service_table', 'appointment_table.serviceID', '=', 'service_table.serviceID')
            ->where('appointment_table.status', 'Completed')
            ->where('appointment_table.appointmentdate', Carbon::today()->format('Y-m-d'))
            ->orderBy('appointment_table.appointmenttime', 'asc')
            ->get();

        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->modalAppointments = [];
    }

    

    

    public function render()
    {
        $today = Carbon::today()->format('Y-m-d');

        // 1. TOP ROW METRICS
        $pendingCount = Appointment::where('status', 'Pending')->count();
        
        $todayPatientsCount = Appointment::where('status', 'Approved')
                                         ->where('appointmentdate', $today)
                                         ->count();
                                         
        $completedTodayCount = Appointment::where('status', 'Completed')
                                          ->where('appointmentdate', $today)
                                          ->count();

        // 2. LEFT COLUMN: The Action Queue (All Pending Appointments)
        $pendingAppointments = Appointment::select(
                'appointment_table.*',
                'users_table.fullname as patient_name',
                'users_table.phone_number',
                'info_table.petname as pet_name',
                'service_table.servicename as service_name'
            )
            ->leftJoin('users_table', 'appointment_table.userID', '=', 'users_table.userID')
            ->leftJoin('info_table', 'appointment_table.infoID', '=', 'info_table.infoID')
            ->leftJoin('service_table', 'appointment_table.serviceID', '=', 'service_table.serviceID')
            ->where('appointment_table.status', 'Pending')
            ->orderBy('appointment_table.created_at', 'asc')
            ->get();

        // 3. RIGHT COLUMN: Today's Locked Itinerary (Approved for Today)
        $todaysItinerary = Appointment::select(
                'appointment_table.*',
                'users_table.fullname as patient_name',
                'info_table.petname as pet_name',
                'service_table.servicename as service_name'
            )
            ->leftJoin('users_table', 'appointment_table.userID', '=', 'users_table.userID')
            ->leftJoin('info_table', 'appointment_table.infoID', '=', 'info_table.infoID')
            ->leftJoin('service_table', 'appointment_table.serviceID', '=', 'service_table.serviceID')
            ->where('appointment_table.status', 'Approved')
            ->where('appointment_table.appointmentdate', $today)
            ->orderBy('appointment_table.appointmenttime', 'asc')
            ->get();

        return view('livewire.assistant.home', [
            'pendingCount' => $pendingCount,
            'todayPatientsCount' => $todayPatientsCount,
            'completedTodayCount' => $completedTodayCount,
            'pendingAppointments' => $pendingAppointments,
            'todaysItinerary' => $todaysItinerary,
            'todayFormatted' => Carbon::today()->format('F d, Y')
        ]);
    }
    
    public function refreshAppointments()
    {
        // Triggers a re-render to fetch latest appointment data
    }
}