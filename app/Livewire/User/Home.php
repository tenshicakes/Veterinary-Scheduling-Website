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
    public function cancelAppointment($id)
    {
        // Ensures the user can only cancel their own appointments
        Appointment::where('appointmentID', $id)
            ->where('userID', Auth::id())
            ->update(['status' => 'Cancelled']);

        session()->flash('success_cancel', 'Appointment has been successfully cancelled.');
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
            'activePetsCount' => $activePetsCount,
            'upcomingVisitsCount' => $upcomingVisitsCount,
            'completedVisitsCount' => $completedVisitsCount,
            'totalAppointmentsCount' => $totalAppointmentsCount,
        ]);
    }
}