<div class="max-w-7xl mx-auto p-4 md:p-8">
    
    <div class="flex flex-col md:flex-row justify-between items-stretch gap-4 mb-8 bg-blue-50 rounded-xl shadow-md border border-gray-200 overflow-hidden">

    <!-- Left Content (Holds the inner padding now) -->
    <div class="p-6 md:p-8 flex flex-col justify-center items-start gap-4 flex-1">
        <div>
            <h2 class="text-2xl font-lg text-blue">Welcome back,</h2>
            <h2 class="text-3xl font-extrabold text-blue">{{ Auth::user()->fullname }}!</h2>
            <p class="text-gray-500 mt-1 font-medium">Here is an overview of your upcoming clinic visits.</p>
        </div>
        
        <a href="{{ route('user.appointment') }}" wire:navigate class="bg-red text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-blue/90 transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Book New Appointment
        </a>
    </div>

    <!-- Right Side: Edge-to-Edge Docked Image -->
    <div class="shrink-0 flex items-end justify-center md:justify-end self-end">
        <img src="{{ asset('images/catdog.png') }}" alt="Clinic Illustration" class="max-h-36 md:max-h-48 w-auto object-contain object-bottom md:object-bottom-right">
    </div>

</div>

    <!-- STATUS INDICATOR OF THE BOOKED APPOINTMENT -->
    @if (session()->has('success_cancel'))
        <div class="p-4 mb-6 text-sm text-green-700 bg-green-100 rounded-lg font-bold shadow-sm">{{ session('success_cancel') }}</div>
    @endif

    <!-- THE GIANT iNFO CARD WHICH IS THE MOST RECENT APPOINTMENT REGARDLESS OF THE STATUS -->
    <h3 class="text-xl font-bold text-blue mb-4">Next Appointment</h3>
    
    @if($nearestAppointment)
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 md:p-8 flex flex-col md:flex-row items-center gap-6 md:gap-10 mb-10 transition hover:shadow-lg">
            
            <div class="w-32 h-32 md:w-40 md:h-40 rounded-full overflow-hidden border-4 border-gray-100 shadow-inner shrink-0">
                @if($nearestAppointment->pet_image)
                    <img src="{{ asset('storage/' . $nearestAppointment->pet_image) }}" alt="Pet Photo" class="w-full h-full object-cover">
                @else
                    <img src="{{ asset('images/DogCat.png') }}" alt="Default Pet" class="w-full h-full object-cover">
                @endif
            </div>

            <div class="flex-1 text-center md:text-left w-full">
                <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-4">
                    <div>
                        <h4 class="text-2xl font-extrabold text-gray-800">{{ $nearestAppointment->pet_name }}</h4>
                        <p class="text-blue font-bold text-lg mt-1">{{ \Carbon\Carbon::parse($nearestAppointment->appointmentdate)->format('F d, Y') }} at {{ \Carbon\Carbon::parse($nearestAppointment->appointmenttime)->format('h:i A') }}</p>
                    </div>
                    <span class="inline-block px-4 py-1.5 rounded-full text-sm font-bold {{ $nearestAppointment->status == 'Approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $nearestAppointment->status }}
                    </span>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 mb-6 text-sm text-gray-700">
                    <span class="font-bold">Service:</span> {{ $nearestAppointment->service_name }}
                </div>

                <!-- Action Buttons for Giant Card -->
                <div class="flex flex-wrap justify-center md:justify-start gap-3">
                    <button wire:click="openRescheduleModal({{ $nearestAppointment->appointmentID }})" 
                            class="bg-blue text-white font-bold py-2 px-6 rounded shadow-sm hover:opacity-90 transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21v-5h5"/></svg>
                        Reschedule
                    </button>

                    <button wire:confirm="Are you sure you want to cancel this appointment? This action cannot be undone." 
                            wire:click="cancelAppointment({{ $nearestAppointment->appointmentID }})" 
                            class="bg-red text-white font-bold py-2 px-6 rounded shadow-sm hover:opacity-90 transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 flex flex-col items-center justify-center text-center mb-10">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            </div>
            <h4 class="text-xl font-bold text-gray-800 mb-2">No Upcoming Appointments</h4>
            <p class="text-gray-500 mb-6 max-w-md">You have no scheduled appointments. Book a new appointment to ensure your pets stay healthy and happy!</p>
        </div>
    @endif

    <!-- CLICKABLE LABEL COUNTERS -->
    <h3 class="text-xl font-bold text-blue mb-4">Account Overview</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        
        <a href="{{ route('user.profile', ['tab' => 'pets']) }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center justify-center text-center transition hover:shadow-md hover:bg-blue-50 border-b-4 border-b-gray-400 cursor-pointer group">
            <div class="text-blue mb-3 group-hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="4" r="2"/><circle cx="18" cy="8" r="2"/><circle cx="20" cy="16" r="2"/><path d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q6.52 17.48 4.46 16.84A3.5 3.5 0 0 1 5.5 10Z"/></svg>
            </div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Active Pets</p>
            <h3 class="text-3xl font-extrabold text-gray-800">{{ $activePetsCount }}</h3>
        </a>

        <button wire:click="openUpcomingModal" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center justify-center text-center transition hover:shadow-md hover:bg-blue-50 border-b-4 border-b-gray-400 cursor-pointer group w-full">
            <div class="text-orange-500 mb-3 group-hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            </div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Upcoming</p>
            <h3 class="text-3xl font-extrabold text-gray-800">{{ $upcomingVisitsCount }}</h3>
        </button>

        <button wire:click="openCompletedModal" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center justify-center text-center transition hover:shadow-md hover:bg-blue-50 border-b-4 border-b-gray-400 cursor-pointer group w-full">
            <div class="text-green-500 mb-3 group-hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Completed</p>
            <h3 class="text-3xl font-extrabold text-gray-800">{{ $completedVisitsCount }}</h3>
        </button>

        <button wire:click="openHistoryModal" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center justify-center text-center transition hover:shadow-md hover:bg-blue-50 border-b-4 border-b-gray-400 cursor-pointer group w-full">
            <div class="text-gray-500 mb-3 group-hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
            </div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total History</p>
            <h3 class="text-3xl font-extrabold text-gray-800">{{ $totalAppointmentsCount }}</h3>
        </button>
    </div>

    <!-- Other Upcoming Appointments -->
    @if($otherAppointments->count() > 0)
        <h3 class="text-lg font-bold text-gray-700 mb-4 border-t border-gray-200 pt-8">Other Upcoming Visits</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($otherAppointments as $appt)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-col transition hover:shadow-md">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="font-bold text-lg text-gray-800">{{ $appt->pet_name }}</h4>
                        <span class="text-xs font-bold px-2 py-1 rounded {{ $appt->status == 'Approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ $appt->status }}</span>
                    </div>
                    <p class="text-sm font-bold text-blue mb-1">{{ \Carbon\Carbon::parse($appt->appointmentdate)->format('M d, Y') }}</p>
                    <p class="text-sm text-gray-500 mb-4">{{ \Carbon\Carbon::parse($appt->appointmenttime)->format('h:i A') }}</p>
                    
                    <div class="mt-auto pt-3 border-t border-gray-100 flex gap-2">
                        <button wire:click="openRescheduleModal({{ $appt->appointmentID }})" 
                                class="flex-1 bg-blue text-white font-bold py-1.5 rounded text-sm hover:opacity-90 transition">
                            Reschedule
                        </button>
                        <button wire:confirm="Cancel this appointment?" 
                                wire:click="cancelAppointment({{ $appt->appointmentID }})" 
                                class="flex-1 bg-red text-white font-bold py-1.5 rounded text-sm hover:opacity-90 transition">
                            Cancel
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif


    <!-- HISTORY SECTION-->

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
                        'isUserView' => true 
                    ])
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end shrink-0">
                    <button wire:click="closeModal" class="px-6 py-2 rounded-lg font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">Close Panel</button>
                </div>
            </div>
        </div>
    @endif


    <!-- RESCHEDULE FORM -->

    @if($isRescheduling)
        <div class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl p-6 md:p-8 mt-10 md:mt-0">
                
                <div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-4">
                    <h3 class="text-2xl font-extrabold text-blue">Reschedule Appointment</h3>
                    <button wire:click="closeRescheduleModal" class="text-gray-400 hover:text-red transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                @error('reschedule')
                    <div class="bg-red-100 border-l-4 border-red text-red-700 p-4 mb-6 rounded">
                        <p class="font-bold">Error</p>
                        <p>{{ $message }}</p>
                    </div>
                @enderror

                <div class="flex flex-col md:flex-row gap-6 mb-8">
                    <div class="w-full md:w-1/2">
                        @include('livewire.shared.calendar', ['availableDates' => $availableDates, 'unavailableDates' => $unavailableDates, 'selectedDate' => $selectedDate, 'currentYearMonth' => $currentYearMonth, 'currentMonthName' => $currentMonthName, 'firstDayOfWeek' => $firstDayOfWeek, 'daysInMonth' => $daysInMonth, 'today' => $today])
                    </div>
                    
                    <div class="w-full md:w-1/2">
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