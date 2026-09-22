<div class="max-w-7xl mx-auto p-4 md:p-8">
    
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-blue">My Account</h2>
        <p class="text-gray-500 mt-1 font-medium">Manage your personal information and pets.</p>
    </div>

    <!-- Tabs -->
    <div class="flex gap-4 mb-8 border-b border-gray-200">
        <button wire:click="$set('activeTab', 'profile')" 
                class="px-4 py-2 font-bold transition border-b-4 {{ $activeTab == 'profile' ? 'border-blue text-blue' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
            My Profile
        </button>
        <button wire:click="$set('activeTab', 'pets')" 
                class="px-4 py-2 font-bold transition border-b-4 {{ $activeTab == 'pets' ? 'border-blue text-blue' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
            My Pets
        </button>
    </div>

    <!-- MY PROFILE TAB -->
    @if($activeTab == 'profile')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- LEFT is profile info and uploading of photo -->
            <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-blue mb-6 border-b border-gray-100 pb-2">Personal Information</h3>
                
                @if (session()->has('success_profile'))
                    <div class="p-3 mb-4 text-sm text-green-700 bg-green-100 rounded-lg font-bold">{{ session('success_profile') }}</div>
                @endif

                <form wire:submit="updateProfile" class="flex flex-col gap-5">
                    
                    <!-- Photo Upload -->
                    <div class="flex items-center gap-6 mb-2">
                        <!-- Displays the photo from storage, or use a default asset if no photo -->
                        <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-blue bg-gray-100 shrink-0">
                            @if ($new_photo)
                                <!-- Show preview before saving -->
                                <img src="{{ $new_photo->temporaryUrl() }}" class="w-full h-full object-cover">
                            @elseif (Auth::user()->profile_image)
                                <!-- Shows saved image from storage -->
                                <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" class="w-full h-full object-cover">
                            @else
                                <!-- Default if no photo -->
                                <img src="{{ asset('images/DogCat.png') }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Profile Photo</label>
                            <input type="file" wire:model="new_photo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue hover:file:bg-blue-100 transition cursor-pointer">
                            @error('new_photo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Input fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-blue mb-2">Full Name</label>
                            <input type="text" wire:model="fullname" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue shadow-sm">
                            @error('fullname') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-blue mb-2">Email Address</label>
                            <input type="email" wire:model="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue shadow-sm">
                            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-blue mb-2">Phone Number</label>
                            <input type="text" wire:model="phone_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue shadow-sm">
                            @error('phone_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-blue mb-2">Address</label>
                            <input type="text" wire:model="address" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue shadow-sm">
                            @error('address') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="submit" class="bg-blue text-white font-bold py-2 px-6 rounded-lg shadow hover:opacity-90 transition">Save Profile</button>
                    </div>
                </form>
            </div>

            <!-- RIGHT is  Security / Password -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 h-fit">
                <h3 class="text-xl font-bold text-blue mb-6 border-b border-gray-100 pb-2">Security</h3>

                @if (session()->has('success_password'))
                    <div class="p-3 mb-4 text-sm text-green-700 bg-green-100 rounded-lg font-bold">{{ session('success_password') }}</div>
                @endif

                <form wire:submit="updatePassword" class="flex flex-col gap-4">
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Current Password</label>
                        <input type="password" wire:model="current_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue shadow-sm" placeholder="Verify current password">
                        @error('current_password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">New Password</label>
                        <input type="password" wire:model="new_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue shadow-sm" placeholder="Min. 8 characters">
                        @error('new_password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" wire:model="new_password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue shadow-sm" placeholder="Retype new password">
                    </div>

                    <button type="submit" class="w-full bg-red-500 text-white font-bold py-2 rounded-lg shadow mt-2 hover:bg-red-600 transition">Update Password</button>
                </form>
            </div>

        </div>
    @endif

    <!-- MY PETS TAB -->
    @if($activeTab == 'pets')
        <div class="mb-6 flex justify-between items-center">
            <h3 class="text-2xl font-bold text-blue">Registered Pets</h3>
            <button wire:click="openAddPetModal" class="bg-blue text-white font-bold py-2 px-5 rounded-lg shadow hover:opacity-90 transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Add Pet
            </button>
        </div>

        @if (session()->has('success_pet'))
            <div class="p-3 mb-6 text-sm text-green-700 bg-green-100 rounded-lg font-bold">{{ session('success_pet') }}</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($pets as $pet)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center text-center transition hover:shadow-md">
                    
                    <!--  Pet Photo -->
                    <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-blue bg-gray-100 mb-4 shrink-0">
                        @if($pet->petimage)
                            <img src="{{ asset('storage/' . $pet->petimage) }}" alt="{{ $pet->petname }}" class="w-full h-full object-cover">
                        @else
                            <img src="{{ asset('images/DogCat.png') }}" alt="Default Pet" class="w-full h-full object-cover">
                        @endif
                    </div>
                    
                    <h4 class="font-bold text-xl text-gray-800">{{ $pet->petname }}</h4>
                    <p class="text-sm text-gray-500 mb-6">{{ $pet->petspecies }} - {{ $pet->petbreed }}</p>
                    
                    <!-- Action Buttons -->
                    <div class="w-full flex flex-col gap-2 mt-auto">
                        <button wire:click="openHistoryModal({{ $pet->infoID }})" class="w-full bg-blue text-white font-bold py-2 rounded shadow-sm hover:opacity-90 transition text-sm">
                            View Medical History
                        </button>
                        <div class="flex gap-2">
                            <button wire:click="openEditPetModal({{ $pet->infoID }})" class="flex-1 bg-gray-100 text-gray-700 font-bold py-2 rounded shadow-sm hover:bg-gray-200 transition text-sm">Edit</button>
                            <button wire:confirm="Are you sure you want to archive this pet? It will be hidden from your active lists." 
                                    wire:click="archivePet({{ $pet->infoID }})" 
                                    class="flex-1 bg-red-50 text-red-600 font-bold py-2 rounded shadow-sm hover:bg-red-100 transition text-sm">Archive</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-gray-50 p-8 rounded-xl border border-dashed border-gray-300 text-center">
                    <p class="text-gray-500 font-medium">You have no active pets registered.</p>
                </div>
            @endforelse
        </div>
    @endif

   
<!-- FORMS  -->


<!--add or edit pet form -->
@if($isPetModalOpen)
    <div class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 md:p-8">
            <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-3">
                <h3 class="text-2xl font-extrabold text-blue">{{ $pet_infoID ? 'Edit Pet' : 'Add New Pet' }}</h3>
                <button wire:click="closePetModal" class="text-gray-400 hover:text-red-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <form wire:submit="savePet" class="flex flex-col gap-4">
                
                <!-- Pet picture Upload -->
                <div class="flex flex-col items-center gap-3 mb-2">
                    <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-blue bg-gray-100 shrink-0">
                        @if ($petimage)
                            <img src="{{ $petimage->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif ($existing_petimage)
                            <img src="{{ asset('storage/' . $existing_petimage) }}" class="w-full h-full object-cover">
                        @else
                            <img src="{{ asset('images/DogCat.png') }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <input type="file" wire:model="petimage" accept="image/*" class="block w-full text-sm text-center text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue file:text-white hover:file:opacity-90 transition cursor-pointer">
                    @error('petimage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-blue mb-1">Pet Name</label>
                    <input type="text" wire:model="petname" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue shadow-sm" required>
                    @error('petname') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-blue mb-1">Species</label>
                        <input type="text" wire:model="petspecies" placeholder="e.g. Dog, Cat" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue shadow-sm" required>
                        @error('petspecies') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-blue mb-1">Breed</label>
                        <input type="text" wire:model="petbreed" placeholder="e.g. Beagle, Persian" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue shadow-sm" required>
                        @error('petbreed') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" wire:click="closePetModal" class="px-6 py-2 rounded-lg font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit" class="px-6 py-2 rounded-lg font-bold text-white bg-blue hover:opacity-90 shadow-md transition">Save Pet</button>
                </div>
            </form>
        </div>
    </div>
@endif

<!-- MEDICAL HISTORY FORM -->
@if($isHistoryModalOpen)
    <div class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 md:p-8 max-h-[85vh] flex flex-col">
            
            <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-3">
                <h3 class="text-2xl font-extrabold text-blue">Medical History: {{ $historyPetName }}</h3>
                <button wire:click="closeHistoryModal" class="text-gray-400 hover:text-red-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <div class="overflow-y-auto pr-2 flex-1">
                @forelse($petHistory as $history)
                    <div class="mb-4 border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-blue text-lg">{{ \Carbon\Carbon::parse($history->appointmentdate)->format('F d, Y') }}</span>
                            <span class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded">Completed</span>
                        </div>
                        <p class="text-sm text-gray-700 mb-1"><span class="font-bold">Service:</span> {{ $history->servicename }}</p>
                        <div class="text-sm text-gray-600 bg-white p-3 rounded border border-gray-100 mt-2">
                            <span class="font-bold">Remarks / Diagnosis:</span><br>
                            {{ $history->notes ?? 'No remarks recorded.' }}
                        </div>
                    </div>
                @empty
                    <div class="text-center p-6 text-gray-500">
                        No past medical history found for this pet.
                    </div>
                @endforelse
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end">
                <button wire:click="closeHistoryModal" class="px-6 py-2 rounded-lg font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">Close</button>
            </div>
        </div>
    </div>
@endif

</div>
</div>

