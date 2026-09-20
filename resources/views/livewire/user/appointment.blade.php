<div class="max-w-4xl mx-auto bg-surface rounded-2xl shadow-[0_4px_20px_rgb(0,0,0,0.08)] p-8">
    
    <!-- HEADER & PROGRESS TRACKER -->
    <div class="mb-10">
        <h2 class="text-3xl font-extrabold text-primary mb-8 text-center">Book an Appointment</h2>
        
        <!-- 4-Step Progress Circles -->
        <div class="relative flex justify-between items-center w-full before:absolute before:inset-0 before:top-1/2 before:-translate-y-1/2 before:h-1 before:bg-gray-200 before:z-0">
            
            <!-- Step 1: Pet -->
            <div class="relative z-10 flex flex-col items-center gap-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold {{ $currentStep >= 1 ? 'bg-primary text-surface' : 'bg-gray-200 text-gray-500' }} transition">1</div>
                <span class="text-xs font-bold {{ $currentStep >= 1 ? 'text-primary' : 'text-gray-400' }}">Choose Pet</span>
            </div>

            <!-- Step 2: Service -->
            <div class="relative z-10 flex flex-col items-center gap-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold {{ $currentStep >= 2 ? 'bg-primary text-surface' : 'bg-gray-200 text-gray-500' }} transition">2</div>
                <span class="text-xs font-bold {{ $currentStep >= 2 ? 'text-primary' : 'text-gray-400' }}">Choose Service</span>
            </div>

            <!-- Step 3: Schedule -->
            <div class="relative z-10 flex flex-col items-center gap-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold {{ $currentStep >= 3 ? 'bg-primary text-surface' : 'bg-gray-200 text-gray-500' }} transition">3</div>
                <span class="text-xs font-bold {{ $currentStep >= 3 ? 'text-primary' : 'text-gray-400' }}">Schedule</span>
            </div>

            <!-- Step 4: Confirm -->
            <div class="relative z-10 flex flex-col items-center gap-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold {{ $currentStep >= 4 ? 'bg-primary text-surface' : 'bg-gray-200 text-gray-500' }} transition">4</div>
                <span class="text-xs font-bold {{ $currentStep >= 4 ? 'text-primary' : 'text-gray-400' }}">Confirm</span>
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
                        <div class="w-20 h-20 bg-gray-200 rounded-full overflow-hidden">
                            <img src="{{ asset('images/DogCat.png') }}" alt="Pet" class="w-full h-full object-cover">
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
            
            <!-- We call the reusable blade component here -->
            <x-shared.service-table :services="$services" :selectedService="$selectedService" />

        @endif

    </div>

    <!-- BOTTOM NAVIGATION BUTTONS -->
    <div class="mt-10 pt-6 border-t border-gray-100 flex items-center {{ $currentStep == 1 ? 'justify-end' : 'justify-between' }}">
        
        @if($currentStep > 1)
            <button wire:click="previousStep" class="px-6 py-2 border-2 border-gray-200 text-gray-600 font-bold rounded-lg hover:bg-gray-50 transition">
                Back
            </button>
        @endif

        @if($currentStep < 4)
            <button wire:click="nextStep" class="px-8 py-2 bg-primary text-surface font-bold rounded-lg shadow-md hover:bg-blue-800 transition">
                Next Step
            </button>
        @endif
    </div>

</div>