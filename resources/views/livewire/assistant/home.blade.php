<div class="max-w-7xl mx-auto p-4 md:p-8" 
     x-data="{ poll: setInterval(() => { 
         if (!document.hidden) $wire.call('refreshAppointments'); 
     }, 30000) }"
     x-init="window.addEventListener('visibilitychange', () => {
         if (!document.hidden) $wire.call('refreshAppointments');
     })"
>
    
    <!-- HEADER -->
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-blue">Overview</h2>
        <p class="text-gray-500 mt-1 font-medium">Manage today's itinerary and pending appointment requests.</p>
    </div>

    <!-- NOTIFICATION AREA -->
    @if (session()->has('success_action'))
        <div class="p-4 mb-6 text-sm text-blue bg-blue-50 border border-blue-200 rounded-lg font-bold shadow-sm">
            {{ session('success_action') }}
        </div>
    @endif

    <!-- TOP ROW: OPERATIONAL METRICS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <!-- Action Required (Pending) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center gap-4">
            
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pending Approvals</p>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $pendingCount }}</h3>
            </div>
        </div>

        <!-- Today's Patients (Approved) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center gap-4 ">
            
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Today's Patients</p>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $todayPatientsCount }}</h3>
            </div>
        </div>

        <!-- Completed Today -->
        <button wire:click="openCompletedTodayModal" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center gap-4 cursor-pointer transition hover:shadow-md hover:bg-blue-50 ">
            
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
          
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Completed Today</p>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $completedTodayCount }}</h3>
            </div>
        </button>
    </div>

    <!-- MAIN TWO-COLUMN LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        
        <!-- LEFT COLUMN: Action Queue (Pending) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-full">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-lg">Action Queue</h3>
                <span class="bg-red text-white text-xs font-bold px-2 py-1 rounded-full">{{ $pendingCount }} Pending</span>
            </div>
            
            <div class="p-6 flex-1 overflow-y-auto max-h-150 flex flex-col gap-4">
                @forelse($pendingAppointments as $appt)
                    <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm transition hover:shadow-md">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-bold text-blue text-lg">{{ $appt->patient_name }}</h4>
                                <p class="text-xs text-gray-500 font-medium">{{ $appt->phone_number }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($appt->appointmentdate)->format('M d, Y') }}</p>
                                <p class="text-sm text-red font-bold">{{ \Carbon\Carbon::parse($appt->appointmenttime)->format('h:i A') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-1 text-sm text-gray-600 mb-3 border-t border-gray-100 pt-2">
                            <p><span class="font-bold">Pet:</span> {{ $appt->pet_name }}</p>
                            <p><span class="font-bold">Service:</span> {{ $appt->service_name }}</p>
                        </div>

                        <!-- GCash/Payment Notes Box -->
                        <div class="bg-gray-50 p-3 rounded border border-gray-200 mb-4 text-sm text-gray-700 warp-break-word">
                            <span class="font-bold text-gray-800 block mb-1">Payment Reference / Notes:</span>
                            {{ $appt->notes ?? 'No notes provided.' }}
                        </div>

                        <!-- One-Click Approvals -->
                        <div class="flex gap-2 mt-auto">
                            <button wire:confirm="Approve this appointment? Ensure payment is verified." 
                                    wire:click="updateStatus({{ $appt->appointmentID }}, 'Approved')" 
                                    class="flex-1 bg-blue text-white font-bold py-2 rounded shadow-sm hover:opacity-90 transition text-sm">
                                Approve
                            </button>
                            <button wire:confirm="Are you sure you want to REJECT this request?" 
                                    wire:click="updateStatus({{ $appt->appointmentID }}, 'Rejected')" 
                                    class="flex-1 bg-red text-white font-bold py-2 rounded shadow-sm hover:opacity-90 transition text-sm">
                                Reject
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center text-center p-8 text-gray-400 h-full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-4"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                        <p class="font-bold text-gray-600 text-lg">Inbox Zero</p>
                        <p class="text-sm">There are no pending appointments to review.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT COLUMN: Today's Itinerary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-full">
            <div class="bg-blue text-white px-6 py-4 flex justify-between items-center">
                <h3 class="font-bold text-lg">Today's Itinerary</h3>
                <span class="text-sm font-bold opacity-90">{{ $todayFormatted }}</span>
            </div>
            
            <div class="p-6 flex-1 overflow-y-auto max-h-150 flex flex-col gap-4">
                @forelse($todaysItinerary as $appt)
                    <div class="border-l-4 border-l-blue bg-gray-50 rounded-r-lg p-4 shadow-sm flex flex-col gap-3 transition hover:shadow-md">
                        
                        <div class="flex justify-between items-center">
                            <span class="bg-blue/10 text-blue font-bold px-3 py-1 rounded-full text-sm">
                                {{ \Carbon\Carbon::parse($appt->appointmenttime)->format('h:i A') }}
                            </span>
                            <span class="text-xs font-bold text-gray-500 uppercase">Approved</span>
                        </div>
                        
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg">{{ $appt->patient_name }}</h4>
                            <p class="text-sm text-gray-600 font-medium">Pet: <span class="font-bold">{{ $appt->pet_name }}</span></p>
                            <p class="text-sm text-gray-600">Service: {{ $appt->service_name }}</p>
                        </div>
                        
                        <!-- Floor Actions -->
                        <div class="flex gap-2 mt-2 pt-3 border-t border-gray-200">
                            <button wire:confirm="Mark this patient as Completed?" 
                                    wire:click="updateStatus({{ $appt->appointmentID }}, 'Completed')" 
                                    class="flex-1 bg-gray-800 text-white font-bold py-1.5 rounded shadow-sm hover:bg-gray-900 transition text-sm">
                                Complete
                            </button>
                            <button wire:confirm="Mark this patient as a No-Show?" 
                                    wire:click="updateStatus({{ $appt->appointmentID }}, 'No-Show')" 
                                    class="flex-1 bg-red text-white font-bold py-1.5 rounded shadow-sm hover:opacity-90 transition text-sm">
                                No-Show
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center text-center p-8 text-gray-400 h-full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-4"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        <p class="font-bold text-gray-600 text-lg">Clear Schedule</p>
                        <p class="text-sm">There are no approved appointments remaining for today.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- COMPLETED TODAY MODAL -->
    @if($isModalOpen)
        <div class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl p-6 md:p-8 max-h-[90vh] flex flex-col mt-10 md:mt-0">
                
                <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                    <h3 class="text-2xl font-extrabold text-blue">{{ $modalTitle }}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-red transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <div class="overflow-y-auto flex-1 pr-2">
                    @include('livewire.shared.appointment-card', [
                        'appointments' => $modalAppointments,
                        'isUserView' => false 
                    ])
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end shrink-0">
                    <button wire:click="closeModal" class="px-6 py-2 rounded-lg font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">Close Panel</button>
                </div>
            </div>
        </div>
    @endif

</div>