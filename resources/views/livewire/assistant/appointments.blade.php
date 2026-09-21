<div class="max-w-7xl mx-auto p-4 md:p-8">
    
    <!-- Header & Search Bar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 w-full">
        <h2 class="text-3xl font-extrabold text-blue">Manage Appointments</h2>
        
        <!-- grouped the Search and Filter in a flex container so they stack on mobile -->
        <div class="w-full md:w-auto flex flex-col md:flex-row gap-3">
            
            <!-- Search Bar -->
            <input type="text" wire:model.live="search" placeholder="Search patient or reference..." 
                   class="w-full md:w-64 border border-gray-300 rounded-lg pl-4 pr-4 py-2 focus:border-primary focus:outline-none shadow-sm">
            
            <!-- Dropdown Filter connected directly to the livewire variable -->
            <select wire:model.live="sortFilter" class="w-full md:w-56 border border-gray-300 rounded-lg pl-4 pr-8 py-2 focus:border-primary focus:outline-none shadow-sm bg-white text-black cursor-pointer">
                <option value="newest_request">Newest Request</option>
                <option value="oldest_request">Oldest Request</option>
                <option value="nearest_appt">Nearest Appointment</option>
                <option value="farthest_appt">Farthest Appointment</option>
            </select>
        </div>
    </div>

    <!-- Status Tabs (Horizontally scrollable on small mobile screens) -->
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
    @include ('livewire.shared.appointment-card')

    <!-- Pagination Links -->
    <div class="mt-8">
        {{ $appointments->links() }}
    </div>
    
</div>