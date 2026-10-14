<?php

namespace App\Livewire\Admin;

use App\Models\Appointment as AppointmentModel;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.adminmaster')]
class Reports extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'All';
    public $datePreset = 'this_month';
    public $startDate;
    public $endDate;
    
    public $availableServices = [];
    public $selectedServices = [];
    
    // Checkbox Memory
    public $selectedAppointments = [];
    public $selectAll = false;

    public function mount()
    {
        // Load all services and check them by default
        $this->availableServices = Service::orderBy('servicename')->get();
        $this->selectedServices = $this->availableServices->pluck('serviceID')->map(fn($id) => (string) $id)->toArray();
        
        $this->applyDatePreset();
    }

    // Automatically update start/end dates when preset dropdown changes
    public function updatedDatePreset()
    {
        $this->applyDatePreset();
        $this->resetPage();
        $this->resetSelection();
    }

    private function applyDatePreset()
    {
        $now = Carbon::now();
        if ($this->datePreset == 'today') {
            $this->startDate = $now->format('Y-m-d');
            $this->endDate = $now->format('Y-m-d');
        } elseif ($this->datePreset == 'this_week') {
            $this->startDate = $now->startOfWeek()->format('Y-m-d');
            $this->endDate = $now->endOfWeek()->format('Y-m-d');
        } elseif ($this->datePreset == 'this_month') {
            $this->startDate = $now->startOfMonth()->format('Y-m-d');
            $this->endDate = $now->endOfMonth()->format('Y-m-d');
        }
    }

    public function updatingSearch() { $this->resetPage(); $this->resetSelection(); }
    public function updatingStatusFilter() { $this->resetPage(); $this->resetSelection(); }
    public function updatingSelectedServices() { $this->resetPage(); $this->resetSelection(); }
    public function updatingStartDate() { $this->datePreset = 'custom'; $this->resetPage(); $this->resetSelection(); }
    public function updatingEndDate() { $this->datePreset = 'custom'; $this->resetPage(); $this->resetSelection(); }

    private function resetSelection()
    {
        $this->selectAll = false;
        $this->selectedAppointments = [];
    }

    // Master "Select All" Checkbox Logic
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedAppointments = $this->baseQuery->pluck('appointmentID')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedAppointments = [];
        }
    }

    // Deselect "Select All" if a user manually unchecks a single row
    public function updatedSelectedAppointments()
    {
        $this->selectAll = false;
    }

    // Centralized Query to ensure Pagination and PDF Export share the exact same data
    public function getBaseQueryProperty()
    {
        $query = AppointmentModel::select(
            'appointment_table.*',
            'users_table.fullname as patient_name',
            'info_table.petname as pet_name',
            'service_table.servicename as service_name'
        )
        ->leftJoin('users_table', 'appointment_table.userID', '=', 'users_table.userID')
        ->leftJoin('info_table', 'appointment_table.infoID', '=', 'info_table.infoID')
        ->leftJoin('service_table', 'appointment_table.serviceID', '=', 'service_table.serviceID')
        // Strictly exclude Pending & Rejected
        ->whereNotIn('appointment_table.status', ['Pending', 'Rejected']);

        if ($this->statusFilter !== 'All') {
            $query->where('appointment_table.status', $this->statusFilter);
        }

        if (!empty($this->startDate) && !empty($this->endDate)) {
            $query->whereBetween('appointment_table.appointmentdate', [$this->startDate, $this->endDate]);
        }

        if (!empty($this->selectedServices)) {
            $query->whereIn('appointment_table.serviceID', $this->selectedServices);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('users_table.fullname', 'like', '%' . $this->search . '%')
                  ->orWhere('info_table.petname', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('appointment_table.appointmentdate', 'desc')
                     ->orderBy('appointment_table.appointmenttime', 'desc');
    }

    public function generatePDF()
    {
        if (empty($this->selectedAppointments)) {
            $this->addError('export', 'Please select at least one record to generate a report.');
            return;
        }

        // Fetch only the specific records the admin checked
        $records = $this->baseQuery->whereIn('appointment_table.appointmentID', $this->selectedAppointments)->get();

        $pdf = Pdf::loadView('pdf.report', [
            'records' => $records,
            'dateRange' => "{$this->startDate} to {$this->endDate}",
            'generatedDate' => Carbon::now()->format('F d, Y h:i A')
        ])->setPaper('a4', 'landscape'); // Landscape mode so columns fit perfectly

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Clinic_Report_' . Carbon::now()->format('Y_m_d') . '.pdf');
    }

    public function render()
    {
        return view('livewire.admin.reports', [
            'appointments' => $this->baseQuery->paginate(10)
        ]);
    }
}