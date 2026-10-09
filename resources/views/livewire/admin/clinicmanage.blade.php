<div class="max-w-7xl mx-auto p-4 md:p-8">
    
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-blue">Clinic Management</h2>
        <p class="text-gray-500 mt-1 font-medium">Manage clinic services, prices, and schedule availability.</p>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-4 mb-8 border-b border-gray-200">
        <button wire:click="$set('activeTab', 'services')" 
                class="px-4 py-2 font-bold transition border-b-4 {{ $activeTab == 'services' ? 'border-blue text-blue' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
            Service & Pricing List
        </button>
        <button wire:click="$set('activeTab', 'schedule')" 
                class="px-4 py-2 font-bold transition border-b-4 {{ $activeTab == 'schedule' ? 'border-blue text-blue' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
            Schedule & Availability
        </button>
    </div>

    <!-- TAB 1: SERVICE MANAGEMENT -->
    @if($activeTab == 'services')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
            
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Clinic Procedures</h3>
                <button wire:click="openAddServiceModal" class="bg-blue text-white font-bold py-2 px-5 rounded-lg shadow-sm hover:opacity-90 transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    Add Service
                </button>
            </div>

            @if (session()->has('success_service'))
                <div class="p-3 mb-4 text-sm text-blue bg-blue-50 border border-blue-200 rounded-lg font-bold">{{ session('success_service') }}</div>
            @endif

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="p-4 font-bold text-black text-sm">Service Name</th>
                            <th class="p-4 font-bold text-black text-sm">Price</th>
                            <th class="p-4 font-bold text-black text-sm text-center">Status</th>
                            <th class="p-4 font-bold text-black text-sm text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="p-4 font-medium text-gray-800">{{ $service->servicename }}</td>
                                <td class="p-4 font-bold text-black">₱{{ number_format($service->price, 2) }}</td>
                                <td class="p-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $service->isactive ? ' text-blue' : ' text-red' }}">
                                        {{ $service->isactive ? 'Active' : 'Hidden' }}
                                    </span>
                                </td>
                                <td class="p-4 text-right flex justify-end gap-2">
                                    <button wire:click="openEditServiceModal({{ $service->serviceID }})" class="bg-gray-100 text-black font-bold py-1.5 px-3 rounded text-sm hover:bg-blue-100 transition">Edit</button>
                                    <button wire:click="toggleServiceStatus({{ $service->serviceID }})" class="{{ $service->isactive ? 'bg-red text-white hover:opacity-90' : 'bg-blue text-white hover:opacity-90' }} font-bold py-1.5 px-3 rounded text-sm transition">
                                        {{ $service->isactive ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-gray-500">No services found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- TAB 2: SCHEDULE & AVAILABILITY -->
    @if($activeTab == 'schedule')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Calendar Block -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8 flex flex-col h-full">
                

                <h4 class="font-bold text-lg text-blue text-center mb-4">{{ $currentMonthName }}</h4>
                
                <div class="grid grid-cols-7 gap-1 text-center text-xs font-bold text-gray-400 mb-2">
                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                </div>

                <div class="grid grid-cols-7 gap-1 text-center">
                    @for ($i = 0; $i < $firstDayOfWeek; $i++)
                        <div class="p-2"></div>
                    @endfor

                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $dateString = $currentYearMonth . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                            $isClosed = in_array($dateString, $closedDates);
                        @endphp

                        <button type="button" wire:click="selectDate('{{ $dateString }}')" 
                                class="w-full p-2 text-sm font-bold rounded cursor-pointer transition 
                                {{ $selectedDate === $dateString ? 'ring-2 ring-blue shadow-md' : 'hover:bg-gray-100' }}
                                {{ $isClosed ? 'bg-red-50 text-red border border-red-200' : 'bg-white text-gray-700 border border-gray-200' }}">
                            {{ $day }}
                        </button>
                    @endfor
                </div>
            </div>

            <!-- Timeslot Override Block -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8 flex flex-col h-full">
                @if(!$selectedDate)
                    <div class="flex-1 flex flex-col items-center justify-center text-gray-400 text-center h-full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-4"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        <p class="font-bold text-lg text-gray-600">No Date Selected</p>
                        <p class="text-sm">Click a date on the calendar to manage its availability.</p>
                    </div>
                @else
                    <div class="mb-6 border-b border-gray-100 pb-4">
                        <h3 class="text-xl font-bold text-blue mb-1">{{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}</h3>
                        <p class="text-sm text-gray-500">Override default scheduling rules for this date.</p>
                    </div>

                    @if (session()->has('success_schedule'))
                        <div class="p-3 mb-4 text-sm {{ $isDayClosed ? 'bg-red-50 text-red border-red-200' : 'bg-blue-50 text-blue border-blue-200' }} border rounded-lg font-bold">{{ session('success_schedule') }}</div>
                    @endif

                    <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                        <div>
                            <p class="font-bold text-gray-800">Close Entire Day</p>
                            <p class="text-xs text-gray-500">Prevent any bookings on this date.</p>
                        </div>
                        <button wire:click="toggleEntireDay" class="font-bold py-2 px-4 rounded text-sm transition {{ $isDayClosed ? 'bg-red text-white hover:opacity-90' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-100' }}">
                            {{ $isDayClosed ? 'Day is Closed' : 'Day is Open' }}
                        </button>
                    </div>

                    @if(!$isDayClosed)
                        <h4 class="font-bold text-gray-800 mb-3">Block Specific Hours</h4>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($allSlots as $slot)
                                @php $isBlocked = in_array($slot, $blockedSlots); @endphp
                                <button wire:click="toggleTimeslot('{{ $slot }}')" 
                                        class="border rounded-lg p-3 text-center font-bold text-sm transition 
                                        {{ $isBlocked ? 'bg-red-50 text-red border-red-200' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
                                    {{ $slot }}
                                    <span class="block text-[10px] uppercase mt-0.5 {{ $isBlocked ? 'text-red' : 'text-green-500' }}">{{ $isBlocked ? 'Blocked' : 'Open' }}</span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @endif

    <!-- ADD / EDIT SERVICE MODAL -->
    @if($isServiceModalOpen)
        <div class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 md:p-8">
                
                <div class="flex justify-between items-center mb-6  pb-3">
                    <h3 class="text-2xl font-extrabold text-blue">{{ $isEditMode ? 'Edit Service' : 'Add New Service' }}</h3>
                    <button wire:click="closeServiceModal" class="text-gray-400 hover:text-red transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="saveService" class="flex flex-col gap-4">
                    <div>
                        <label class="block text-sm font-bold text-blue mb-1">Service Name</label>
                        <input type="text" wire:model="servicename" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue" required>
                        @error('servicename') <span class="text-red text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-blue mb-1">Price (₱)</label>
                        <input type="number" step="0.01" wire:model="price" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue" required>
                        @error('price') <span class="text-red text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 mt-4">
                        <button type="button" wire:click="closeServiceModal" class="px-6 py-2 rounded-lg font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">Cancel</button>
                        <button type="submit" class="px-6 py-2 rounded-lg font-bold text-white bg-blue hover:opacity-90 shadow-md transition">Save Service</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>