<div class="max-w-4xl mx-auto bg-surface rounded-2xl shadow-[0_4px_20px_rgb(0,0,0,0.08)] p-4 md:p-8">
    
    <!-- HEADER & PROGRESS TRACKER -->
    <div class="mb-10">
        <h2 class="text-3xl font-extrabold text-blue mb-8 text-center">Book an Appointment</h2>
        
        <!-- 4-Step Progress Circles -->
        <div class="relative flex justify-between items-start w-full before:absolute before:left-0 before:right-0 before:top-5 before:-translate-y-1/2 before:h-1 before:bg-gray-200 before:z-0">
            
            <!-- Step 1: Pet -->
            <div class="relative z-10 flex flex-col items-center gap-2 w-20">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold {{ $currentStep >= 1 ? 'bg-blue text-white shadow-md' : 'bg-gray-200 text-gray-500' }} transition">1</div>
                <span class="text-xs font-bold text-center {{ $currentStep >= 1 ? 'text-primary' : 'text-gray-400' }}">Choose Pet</span>
            </div>

            <!-- Step 2: Service -->
            <div class="relative z-10 flex flex-col items-center gap-2 w-20">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold {{ $currentStep >= 2 ? 'bg-blue text-white shadow-md' : 'bg-gray-200 text-gray-500' }} transition">2</div>
                <span class="text-xs font-bold text-center {{ $currentStep >= 2 ? 'text-primary' : 'text-gray-400' }}">Choose Service</span>
            </div>

            <!-- Step 3: Schedule -->
            <div class="relative z-10 flex flex-col items-center gap-2 w-20">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold {{ $currentStep >= 3 ? 'bg-blue text-white shadow-md' : 'bg-gray-200 text-gray-500' }} transition">3</div>
                <span class="text-xs font-bold text-center {{ $currentStep >= 3 ? 'text-primary' : 'text-gray-400' }}">Schedule</span>
            </div>

            <!-- Step 4: Confirm -->
            <div class="relative z-10 flex flex-col items-center gap-2 w-20">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold {{ $currentStep >= 4 ? 'bg-blue text-white shadow-md' : 'bg-gray-200 text-gray-500' }} transition">4</div>
                <span class="text-xs font-bold text-center {{ $currentStep >= 4 ? 'text-primary' : 'text-gray-400' }}">Confirm</span>
            </div>

        </div>
    </div>

    <!-- ERROR MESSAGE -->
    @error('selection')
        <div class="mb-6 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm text-center font-medium">
            {{ $message }}
        </div>
    @enderror

    <!-- DYNAMIC STEP CONTENT -->
    <div class="min-h-75">
        
        <!-- STEP 1: CHOOSE PET -->
        @if($currentStep == 1)
            <h3 class="text-xl font-bold text-gray-800 mb-4">Which pet is this appointment for?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse($pets as $pet)
                    <!-- Clicking this div sets the selectedPet variable instantly -->
                    <div wire:click="$set('selectedPet', '{{ $pet->infoID }}')" 
                         class="cursor-pointer border-2 rounded-xl p-4 flex flex-col items-center gap-3 transition hover:shadow-md 
                         {{ $selectedPet == $pet->infoID ? 'border-primary bg-blue-50' : 'border-gray-200 bg-surface' }}">
                        
                        <!-- Placeholder for Pet Image -->
                        <div class="w-20 h-20 bg-gray-200 rounded-full overflow-hidden shrink-0 border border-gray-100">

                            <!-- if 'petimage' exists in the database, it loads from Laravel's storage. If null, it uses the default image. -->
                            @if($pet->petimage)
                                <img src="{{ asset('storage/' . $pet->petimage) }}" alt="{{ $pet->petname }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/DogCat.png') }}" alt="Default Pet" class="w-full h-full object-cover">
                            @endif
                        </div>
                        
                        <div class="text-center">
                            <h4 class="font-bold text-gray-800">{{ $pet->petname }}</h4>
                            <p class="text-sm text-gray-500">{{ $pet->petspecies }} - {{ $pet->petbreed }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center p-8 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                        <p class="text-gray-500">You don't have any pets registered yet.</p>
                    </div>
                @endforelse
            </div>

        <!-- STEP 2: CHOOSE SERVICE -->
        @elseif($currentStep == 2)
            <h3 class="text-xl font-bold text-gray-800 mb-4">Select a Service</h3>
            <x-shared.service-table :services="$services" :selectedService="$selectedService" />

        <!-- STEP 3: CHOOSE DATE & TIME -->
        @elseif($currentStep == 3)
            <h3 class="text-xl font-bold text-gray-800 mb-4">Select Date and Time</h3>
            
            <div class="flex flex-col md:flex-row gap-6">
                <!-- LEFT SIDE: CALENDAR -->
                <div class="w-full md:w-1/2">
                    @include('livewire.shared.calendar', ['availableDates' => $availableDates, 'unavailableDates' => $unavailableDates, 'selectedDate' => $selectedDate, 'currentYearMonth' => $currentYearMonth, 'currentMonthName' => $currentMonthName, 'firstDayOfWeek' => $firstDayOfWeek, 'daysInMonth' => $daysInMonth, 'today' => $today])
                </div>

                <!-- RIGHT SIDE: TIME SLOTS -->
                <div class="w-full md:w-1/2">
                    @include('livewire.shared.timeslot')
                </div>
            </div>

        
        <!-- STEP 4: CONFIRMATION & PAYMENT -->
        @elseif($currentStep == 4)
            <h3 class="text-xl font-bold text-gray-800 mb-6 text-center">Review & Payment</h3>
            
            <div class="flex flex-col md:flex-row gap-8">
                
                <!-- LEFT SIDE: PREVIEW SUMMARY -->
                <div class="w-full md:w-1/2 bg-blue-50 border border-blue-100 rounded-xl p-4 md:p-6 h-fit shadow-sm">
                    <h4 class="font-bold text-primary mb-4 border-b border-blue-200 pb-2">Appointment Details</h4>
                    
                    <div class="flex flex-col gap-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium text-sm">Date & Time</span>
                            <span class="font-bold text-gray-800 text-right">
                                {{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}<br>
                                {{ $selectedTime }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium text-sm">Pet</span>
                            <span class="font-bold text-gray-800 text-right">
                                {{ $pets->firstWhere('infoID', $selectedPet)->petname ?? 'Unknown' }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center border-b border-blue-200 pb-4">
                            <span class="text-gray-500 font-medium text-sm">Service</span>
                            <span class="font-bold text-gray-800 text-right">
                                {{ $services->firstWhere('serviceID', $selectedService)->servicename ?? $services->firstWhere('id', $selectedService)->servicename ?? 'Unknown' }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center pt-2">
                            <span class="text-gray-800 font-bold">Total Amount Due</span>
                            <span class="font-extrabold text-green-600 text-2xl">
                                ₱{{ number_format($services->firstWhere('serviceID', $selectedService)->price ?? $services->firstWhere('id', $selectedService)->price ?? 0, 2) }}
                            </span>
                        </div>

                    </div>
                </div>

                <!-- RIGHT SIDE: INSTAPAY QR & INPUTS -->
                <div class="w-full md:w-1/2 bg-white border border-gray-200 rounded-xl p-4 md:p-6 shadow-sm flex flex-col items-center">
                    <h4 class="font-bold text-gray-800 mb-2">Scan to Pay via Instapay</h4>
                    <p class="text-xs text-gray-500 text-center mb-4">Please scan the QR code below to process your payment.</p>
                    
                    <!-- QR Code Image -->
                    <div class="w-40 h-40 border-2 border-dashed border-gray-300 rounded-lg p-2 mb-6 flex items-center justify-center bg-gray-50">
                        <img src="{{ asset('images/DogCat.png') }}" alt="QR Code" class="max-w-full max-h-full object-contain">
                    </div>

                    <div class="w-full flex flex-col gap-4">
                        <!-- Reference Number -->
                        <div>
                            <label class="block text-sm font-bold text-primary mb-1">Payment Reference Number <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="referenceNumber" class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-primary focus:outline-none shadow-sm" placeholder="e.g. 10023948291" required>
                            @error('referenceNumber') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Extra Notes -->
                        <div>
                            <label class="block text-sm font-bold text-primary mb-1">Extra Notes (Optional)</label>
                            <textarea wire:model="notes" rows="2" class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-primary focus:outline-none shadow-sm resize-none" placeholder="e.g. My dog is a bit aggressive..."></textarea>
                        </div>
                    </div>
                </div>

            </div>
        @endif

    </div>

    <!-- BOTTOM NAVIGATION BUTTONS -->
    <div class="mt-10 pt-6 border-t border-gray-100 flex items-center justify-between">
        
        @if($currentStep > 1)
            <button wire:click="previousStep" class="px-6 py-2 border-2 border-gray-200 text-gray-600 font-bold rounded-lg hover:bg-red hover:text-white transition">
                Back
            </button>
        @else
            <div></div> <!-- Spacer -->
        @endif

        @if($currentStep < 4)
            <button wire:click="nextStep" class="px-8 py-2 bg-white text-primary font-bold rounded-lg shadow-md hover:bg-blue hover:text-white transition">
                Next Step
            </button>
        @else
            <!-- Final Submit Button -->
            <button wire:click="confirmAppointment" class="px-8 py-2 bg-green-600 text-surface font-bold rounded-lg shadow-md hover:bg-green-700 transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Confirm Booking
            </button>
        @endif
    </div>

</div>