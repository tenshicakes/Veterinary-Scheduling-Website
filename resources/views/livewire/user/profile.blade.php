<div class="max-w-7xl mx-auto p-4 md:p-8">
    
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-blue">My Account</h2>
        <p class="text-gray-500 mt-1 font-medium">Manage your personal information and pets.</p>
    </div>

    <!-- Tab Navigation -->
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

    <!-- TAB 1: MY PROFILE -->
    @if($activeTab == 'profile')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- LEFT COLUMN: Profile Info & Photo Upload -->
            <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-blue mb-6 border-b border-gray-100 pb-2">Personal Information</h3>
                
                @if (session()->has('success_profile'))
                    <div class="p-3 mb-4 text-sm text-green-700 bg-green-100 rounded-lg font-bold">{{ session('success_profile') }}</div>
                @endif

                <form wire:submit="updateProfile" class="flex flex-col gap-5">
                    
                    <!-- Photo Upload Section -->
                    <div class="flex items-center gap-6 mb-2">
                        <!-- Displays the dynamic photo from storage, or falls back to a default asset if null -->
                        <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-blue bg-gray-100 shrink-0">
                            @if ($new_photo)
                                <!-- Shows temporary live preview before saving -->
                                <img src="{{ $new_photo->temporaryUrl() }}" class="w-full h-full object-cover">
                            @elseif (Auth::user()->profile_image)
                                <!-- Shows saved dynamic image from storage -->
                                <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" class="w-full h-full object-cover">
                            @else
                                <!-- Default fallback if no photo exists -->
                                <img src="{{ asset('images/DogCat.png') }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Profile Photo</label>
                            <input type="file" wire:model="new_photo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue hover:file:bg-blue-100 transition cursor-pointer">
                            @error('new_photo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Input Fields -->
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

            <!-- RIGHT COLUMN: Security / Password -->
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

    <!-- TAB 2: MY PETS (Placeholder for next step) -->
    @if($activeTab == 'pets')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center text-gray-500">
            <h3 class="text-xl font-bold text-blue mb-2">My Pets & Medical History</h3>
            <p>Pet management features will be added here next!</p>
        </div>
    @endif

</div>