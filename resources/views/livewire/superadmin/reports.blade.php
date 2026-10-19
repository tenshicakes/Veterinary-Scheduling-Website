<div class="max-w-7xl mx-auto p-4 md:p-8">
    
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-blue">Reports</h2>
            <p class="text-gray-500 mt-1 font-medium">Filter, select, and export clinic records.</p>
        </div>
        <button wire:click="generatePDF" class="bg-red text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-red-700 transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download preview-icon"><path d="M12 15V3"/><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/>
        </svg>
            Generate PDF Report
        </button>
    </div>

    @error('export')
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded font-bold">
            {{ $message }}
        </div>
    @enderror

    <!-- Filters Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8 grid grid-cols-1 md:grid-cols-4 gap-6">
        
        <!-- Search -->
        <div>
            <label class="block text-xs font-bold text-black uppercase mb-2">Search</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Patient or Pet name..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue text-sm">
        </div>

        <!-- Status -->
        <div>
            <label class="block text-xs font-bold text-black uppercase mb-2">Status</label>
            <select wire:model.live="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue text-sm bg-white">
                <option value="All">All Valid States</option>
                <option value="Completed">Completed</option>
                <option value="No-Show">No-Show</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>

        <!-- Date Range -->
        <div class="md:col-span-2">
    <label class="block text-xs font-bold text-black uppercase mb-2">Date Range</label>
    
   
    <div class="flex flex-col md:flex-row gap-3">
        
        <select wire:model.live="datePreset" class="w-full md:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue text-sm bg-white">
            <option value="today">Today</option>
            <option value="this_week">This Week</option>
            <option value="this_month">This Month</option>
            <option value="custom">Custom</option>
        </select>
        
        <input type="date" wire:model.live="startDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue text-sm">
        <input type="date" wire:model.live="endDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue text-sm">
    </div>
</div>

        <!-- Service Checkboxes (Alpine Dropdown) -->
<div class="md:col-span-4  border-gray-100 pt-4" x-data="{ showServices: false }">
    <label class="block text-xs font-bold text-black uppercase mb-3">Included Services</label>
    
    <!-- Dropdown Toggle Button -->
    <button type="button" @click="showServices = !showServices" 
            class="w-full flex justify-between items-center px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 transition focus:outline-none">
        <span class="text-sm font-bold text-gray-700">
            Select Services to Include
        </span>
        <!-- Animated Chevron Icon -->
        <svg class="w-5 h-5 text-gray-500 transition-transform duration-200" 
             :class="showServices ? 'rotate-180' : ''" 
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Expandable Checkbox Container -->
    <div x-show="showServices" 
         x-collapse 
         class="mt-2 p-5 border border-gray-200 rounded-lg bg-white" 
         style="display: none;">
        <div class="flex flex-wrap gap-4">
            @foreach($availableServices as $service)
                <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-gray-700 hover:text-blue transition">
                    <input type="checkbox" wire:model.live="selectedServices" value="{{ $service->serviceID }}" class="w-4 h-4 text-blue rounded border-gray-300 focus:ring-blue">
                    {{ $service->servicename }}
                </label>
            @endforeach
        </div>
    </div>
</div>
    </div>

    <!-- Data Grid -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="p-4 w-12 text-center">
                            <!-- Master Select All Checkbox -->
                            <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 text-blue rounded border-gray-300 focus:ring-blue cursor-pointer">
                        </th>
                        <th class="p-4 font-bold text-black text-sm">Date & Time</th>
                        <th class="p-4 font-bold text-black text-sm">Patient / Pet</th>
                        <th class="p-4 font-bold text-black text-sm">Service</th>
                        <th class="p-4 font-bold text-black text-sm text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="p-4 text-center">
                                <!-- Row-level Memory Checkbox -->
                                <input type="checkbox" wire:model.live="selectedAppointments" value="{{ $appt->appointmentID }}" class="w-4 h-4 text-blue rounded border-gray-300 focus:ring-blue cursor-pointer">
                            </td>
                            <td class="p-4">
                                <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($appt->appointmentdate)->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($appt->appointmenttime)->format('h:i A') }}</p>
                            </td>
                            <td class="p-4">
                                <p class="font-bold text-black">{{ $appt->patient_name }}</p>
                                <p class="text-xs text-gray-500">Pet: {{ $appt->pet_name }}</p>
                            </td>
                            <td class="p-4 font-medium text-gray-800 text-sm">{{ $appt->service_name }}</td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold border 
                                    {{ $appt->status == 'Completed' ? 'bg-green-50 text-green-700 border-green-200' : '' }}
                                    {{ $appt->status == 'No-Show' ? 'bg-gray-100 text-gray-700 border-gray-300' : '' }}
                                    {{ $appt->status == 'Approved' ? 'bg-blue-50 text-blue border-blue-200' : '' }}
                                    {{ $appt->status == 'Cancelled' ? 'bg-red-50 text-red border-red-200' : '' }}">
                                    {{ $appt->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">No records found matching these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination controls -->
        <div class="p-4 border-t border-gray-100">
            {{ $appointments->links() }}
        </div>
    </div>
</div>