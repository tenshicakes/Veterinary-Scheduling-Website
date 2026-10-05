<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.adminmaster')] 
class Clinicmanage extends Component
{
    public $activeTab = 'services';

   
    public $isServiceModalOpen = false;
    public $isEditMode = false;
    public $serviceID = null;
    public $servicename = '';
    public $price = '';
    public $description = '';


    
    public $selectedDate = null;
    private $overrideFile = 'schedule_overrides.json';

   
    private function getOverrides()
    {
        if (Storage::exists($this->overrideFile)) {
            return json_decode(Storage::get($this->overrideFile), true);
        }
        return ['closed_dates' => [], 'blocked_timeslots' => []];
    }

    private function saveOverrides($data)
    {
        Storage::put($this->overrideFile, json_encode($data, JSON_PRETTY_PRINT));
    }

 
    public function openAddServiceModal()
    {
        $this->resetErrorBag();
        $this->reset(['serviceID', 'servicename', 'price', 'description', 'isEditMode']);
        $this->isServiceModalOpen = true;
    }

    public function openEditServiceModal($id)
    {
        $this->resetErrorBag();
        $service = Service::where('serviceID', $id)->first();
        if ($service) {
            $this->serviceID = $service->serviceID;
            $this->servicename = $service->servicename;
            $this->price = $service->price;
            $this->description = $service->description;
            $this->isEditMode = true;
            $this->isServiceModalOpen = true;
        }
    }

    public function closeServiceModal()
    {
        $this->isServiceModalOpen = false;
    }

    public function saveService()
    {
        $this->validate([
            'servicename' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        if ($this->isEditMode) {
            Service::where('serviceID', $this->serviceID)->update([
                'servicename' => $this->servicename,
                'price' => $this->price,
                'description' => $this->description,
            ]);
            session()->flash('success_service', 'Service updated successfully.');
        } else {
            Service::create([
                'servicename' => $this->servicename,
                'price' => $this->price,
                'description' => $this->description,
                'isactive' => true,
            ]);
            session()->flash('success_service', 'New service added successfully.');
        }

        $this->closeServiceModal();
    }

    public function toggleServiceStatus($id)
    {
        $service = Service::where('serviceID', $id)->first();
        if ($service) {
            $service->update(['isactive' => !$service->isactive]);
            session()->flash('success_service', 'Service status updated.');
        }
    }

   

    public function selectDate($date)
    {
        $this->selectedDate = $date;
    }

    public function toggleEntireDay()
    {
        if (!$this->selectedDate) return;

        $overrides = $this->getOverrides();

        if (in_array($this->selectedDate, $overrides['closed_dates'])) {
            // Re-open the day
            $overrides['closed_dates'] = array_values(array_diff($overrides['closed_dates'], [$this->selectedDate]));
            session()->flash('success_schedule', 'Date is now OPEN for bookings.');
        } else {
            // Close the day
            $overrides['closed_dates'][] = $this->selectedDate;
            unset($overrides['blocked_timeslots'][$this->selectedDate]); // Clear specific time blocks if whole day is closed
            session()->flash('success_schedule', 'Date is now CLOSED (Holiday/Blocked).');
        }

        $this->saveOverrides($overrides);
    }

    public function toggleTimeslot($time)
    {
        if (!$this->selectedDate) return;

        $overrides = $this->getOverrides();

        if (!isset($overrides['blocked_timeslots'][$this->selectedDate])) {
            $overrides['blocked_timeslots'][$this->selectedDate] = [];
        }

        if (in_array($time, $overrides['blocked_timeslots'][$this->selectedDate])) {
            // Unblock time
            $overrides['blocked_timeslots'][$this->selectedDate] = array_values(array_diff($overrides['blocked_timeslots'][$this->selectedDate], [$time]));
        } else {
            // Block time
            $overrides['blocked_timeslots'][$this->selectedDate][] = $time;
        }

        // Cleanup empty arrays to keep JSON tidy
        if (empty($overrides['blocked_timeslots'][$this->selectedDate])) {
            unset($overrides['blocked_timeslots'][$this->selectedDate]);
        }

        $this->saveOverrides($overrides);
    }

    public function render()
    {
        $services = Service::orderBy('servicename', 'asc')->get();

        $startOfMonth = Carbon::now()->startOfMonth();
        $overrides = $this->getOverrides();
        
        $closedDates = $overrides['closed_dates'];
        
        $allSlots = ['09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '01:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM'];
        $blockedSlots = [];
        $isDayClosed = false;

        if ($this->selectedDate) {
            $isDayClosed = in_array($this->selectedDate, $closedDates);
            $blockedSlots = $overrides['blocked_timeslots'][$this->selectedDate] ?? [];
        }

        return view('livewire.admin.clinicmanage', [
            'services' => $services,
            'daysInMonth' => $startOfMonth->daysInMonth,
            'firstDayOfWeek' => $startOfMonth->dayOfWeek,
            'currentMonthName' => $startOfMonth->format('F Y'),
            'currentYearMonth' => $startOfMonth->format('Y-m'),
            'closedDates' => $closedDates,
            'isDayClosed' => $isDayClosed,
            'allSlots' => $allSlots,
            'blockedSlots' => $blockedSlots,
        ]);
    }
}