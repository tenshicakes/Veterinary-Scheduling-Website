<div class="max-w-5xl mx-auto p-4 md:p-8">
    
    <!-- HEADER  -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-extrabold text-blue">Clinic Services & Pricing</h2>
            <p class="text-gray-500 mt-1 font-medium">View our available veterinary procedures and their corresponding prices.</p>
        </div>
        
      
        <a href="{{ route('user.appointment') }}" wire:navigate class="bg-red text-white font-bold py-3 px-6 rounded-lg shadow-md hover:opacity-90 transition flex items-center gap-2 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Book an Appointment
        </a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
        
        <!-- EMERGENCY SERVICE BANNER -->
        <div class="bg-red-50 border-l-4 border-red rounded-r-lg p-4 sm:p-6 mb-8 shadow-sm">
            
            <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-5">
                
    
                <div class="p-2.5 sm:p-3 bg-red text-white rounded-full shrink-0 shadow-sm self-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" class="sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </div>
                
                <!-- Content -->
                <div class="flex-1 w-full">
                    <h3 class="text-base sm:text-lg font-bold text-red leading-tight">After-Hours Emergency Service</h3>
                    <p class="text-sm sm:text-base text-gray-700 mt-1.5">
                        We offer emergency veterinary assistance outside of standard operating hours for a flat rate of <span class="font-bold text-red">₱650.00</span>.
                    </p>
                    
                    <!-- Boxed warning  -->
                    <div class="mt-3 bg-white/70 border border-red-200 rounded-md p-3">
                        <p class="text-xs sm:text-sm text-red-700 font-bold">
                            This service cannot be booked online. Please call the clinic directly for immediate assistance.
                        </p>
                    </div>
                </div>
                
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-2">Standard Rates</h3>
            <p class="text-sm text-gray-500">Prices are subject to change based on the specific needs and condition of your pet during the actual physical consultation.</p>
        </div>


        <div class="mb-4">
            @include('livewire.shared.service-table', ['services' => $services, 'isReadOnly' => true]) 
        </div>
        

        <div class="mt-6 flex items-start gap-3 bg-blue-50 p-4 rounded-lg border border-blue-100">
            <div class="text-blue mt-0.5 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
            </div>
            <p class="text-sm text-blue font-medium">
                Additional fees may apply for specialized treatments, laboratory tests, or emergency procedures not listed above. 
            </p>
        </div>

    </div>
</div>

