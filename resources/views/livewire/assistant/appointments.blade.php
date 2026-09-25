<div class="max-w-7xl mx-auto p-4 md:p-8" 
     x-data="{ poll: setInterval(() => { 
         if (!document.hidden) $wire.call('refreshAppointments'); 
     }, 30000) }"
     x-init="window.addEventListener('visibilitychange', () => {
         if (!document.hidden) $wire.call('refreshAppointments');
     })"
>
    
    <!-- Header anD Search Bar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 w-full">
        <h2 class="text-3xl font-extrabold text-blue">Manage Appointments</h2>
        
        <div class="w-full md:w-auto flex flex-col md:flex-row gap-3">
            
            <!-- Searchbar -->
            <input type="text" wire:model.live="search" placeholder="Search patient or reference..." 
                   class="w-full md:w-64 border border-gray-300 rounded-lg pl-4 pr-4 py-2 focus:border-primary focus:outline-none shadow-sm">
            
            <!-- Dropdown Filter  -->
            <select wire:model.live="sortFilter" class="w-full md:w-56 border border-gray-300 rounded-lg pl-4 pr-8 py-2 focus:border-primary focus:outline-none shadow-sm bg-white text-black cursor-pointer">
                <option value="newest_request">Newest Request</option>
                <option value="oldest_request">Oldest Request</option>
                <option value="nearest_appt">Nearest Appointment</option>
                <option value="farthest_appt">Farthest Appointment</option>
            </select>
        </div>
    </div>

    <!-- Status tabs  -->
    <div class="flex gap-2 mb-6 border-b border-gray-200 overflow-x-auto whitespace-nowrap">
        <button wire:click="setTab('Pending')" class="px-6 py-3 font-bold transition border-b-4 {{ $activeTab == 'Pending' ? 'border-primary text-blue' : 'border-transparent text-primary hover:text-gray-700' }}">
            Pending Requests
        </button>
        <button wire:click="setTab('Upcoming')" class="px-6 py-3 font-bold transition border-b-4 {{ $activeTab == 'Upcoming' ? 'border-primary text-blue' : 'border-transparent text-primary hover:text-gray-700' }}">
            Upcoming (Approved)
        </button>
        <button wire:click="setTab('History')" class="px-6 py-3 font-bold transition border-b-4 {{ $activeTab == 'History' ? 'border-primary text-blue' : 'border-transparent text-primary hover:text-gray-700' }}">
            History
        </button>
    </div>

    <!-- Cards Grid (1 col mobile, 2 tablet, 3 desktop) -->
    
    @include('livewire.shared.appointment-card')


    

    <!-- Pagination  -->
    <div class="mt-8">
        {{ $appointments->links() }}
    </div>

    <!-- RESCHEDULE FORM POPUP -->
    @if($isRescheduling)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto">
            <!-- Dark backdrop -->
            <div class="fixed inset-0 bg-gray-900/60" wire:click="closeRescheduleModal"></div>

            <!-- Modal Content Card -->
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-4xl p-6 md:p-8 my-8 z-10 max-h-[90vh] overflow-y-auto">
                
                <div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-4">
                    <h3 class="text-2xl font-extrabold text-primary">Reschedule Appointment</h3>
                    <button wire:click="closeRescheduleModal" class="text-gray-400 hover:text-red-500 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <p class="text-gray-600 mb-6 text-lg">Moving appointment for: <span class="font-bold text-gray-800">{{ $reschedulePatientName }}</span></p>

                @error('reschedule')
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                        <p class="font-bold">Error</p>
                        <p>{{ $message }}</p>
                    </div>
                @enderror

                <!-- Reusing shared components -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        @include('livewire.shared.calendar')
                    </div>
                    
                    <div>
                        @include('livewire.shared.timeslot')
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-200 pt-4">
                    <button wire:click="closeRescheduleModal" class="px-6 py-2 rounded-lg font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">Cancel</button>
                    <button wire:click="confirmReschedule" class="px-6 py-2 rounded-lg font-bold text-white bg-blue hover:opacity-90 shadow-md transition">Confirm Reschedule</button>
                </div>

            </div>
        </div>
    @endif
    
</div>