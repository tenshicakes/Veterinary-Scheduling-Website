<div class="max-w-7xl mx-auto p-4 md:p-8">
    
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-blue">User & Staff Management</h2>
        <p class="text-gray-500 mt-1 font-medium">Manage clinic employees and registered pet owners.</p>
    </div>

    @if (session()->has('success_user'))
        <div class="p-4 mb-6 text-sm text-blue bg-blue-50 border border-blue-200 rounded-lg font-bold shadow-sm">
            {{ session('success_user') }}
        </div>
    @endif

    <!-- MAIN CARD -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
        

        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name or email..." 
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue w-full sm:w-64 text-sm">
                
                <select wire:model.live="roleFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue text-sm bg-white cursor-pointer">
                    <option value="">All Roles</option>
                    <option value="Admin">Admins</option>
                    <option value="Assistant">Assistants</option>
                    <option value="User">Users</option>
                </select>
            </div>

            <button wire:click="openAddModal" class="w-full md:w-auto bg-blue text-white font-bold py-2.5 px-6 rounded-lg shadow-sm hover:opacity-90 transition flex items-center justify-center gap-2 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                Add Account
            </button>
        </div>

        <!-- Users Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="p-4 font-bold text-black text-sm">Name & Email</th>
                        <th class="p-4 font-bold text-black text-sm">Phone Number</th>
                        <th class="p-4 font-bold text-black text-sm">Role</th>
                        <th class="p-4 font-bold text-black text-sm text-center">Status</th>
                        <th class="p-4 font-bold text-black text-sm text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="p-4">
                                <p class="font-bold text-black">{{ $user->fullname }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </td>
                            <td class="p-4 text-sm text-black">{{ $user->phone_number ?? 'N/A' }}</td>
                            <td class="p-4">
                                <span class="  text-xs font-bold 
                                    {{ $user->role == 'Admin' ? ' text-black' : '' }}
                                    {{ $user->role == 'Assistant' ? 'text-black' : '' }}
                                    {{ $user->role == 'User' ? 'text-black' : '' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $user->is_active ? ' text-blue' : ' text-red' }}">
                                    {{ $user->is_active ? 'Active' : 'Deactivated' }}
                                </span>
                            </td>
                            <td class="p-4 text-right flex justify-end gap-2">
                                <button wire:click="openEditModal({{ $user->userID }})" class="bg-gray-100 text-black font-bold py-1.5 px-3 rounded text-sm hover:bg-blue-100 transition">
                                    Edit
                                </button>
                                
                                <button wire:confirm="Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} this account?" 
                                        wire:click="toggleActiveStatus({{ $user->userID }})" 
                                        class="{{ $user->is_active ? 'bg-red text-white hover:opacity-90' : 'bg-blue text-white hover:opacity-90' }} font-bold py-1.5 px-3 rounded text-sm transition min-w-[90px]">
                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-4 text-gray-300"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                                No accounts found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- ADD / EDIT USER MODAL                          -->
    <!-- ============================================== -->
    @if($isModalOpen)
        <div class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 md:p-8 mt-10 md:mt-0">
                
                <div class="flex justify-between items-center mb-6 pb-3">
                    <h3 class="text-2xl font-extrabold text-blue">{{ $isEditMode ? 'Edit Account' : 'Create an account' }}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-red transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="saveUser" class="flex flex-col gap-5">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Full Name -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Full Name</label>
                            <input type="text" wire:model="fullname" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue" required>
                            @error('fullname') <span class="text-red text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Email Address</label>
                            <input type="email" wire:model="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue" required>
                            @error('email') <span class="text-red text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Phone Number</label>
                            <input type="text" wire:model="phone_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue">
                            @error('phone_number') <span class="text-red text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Role -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">System Role</label>
                            <select wire:model="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue bg-white cursor-pointer" required>
                                <option value="User">User (Client)</option>
                                <option value="Assistant">Assistant (Staff)</option>
                                <option value="Admin">Admin (Owner)</option>
                            </select>
                            @error('role') <span class="text-red text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Address</label>
                        <input type="text" wire:model="address" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue">
                        @error('address') <span class="text-red text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
                        <label class="block text-sm font-bold text-gray-700 mb-1">
                            {{ $isEditMode ? 'New Password' : 'Account Password' }}
                        </label>
                        <input type="password" wire:model="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue" {{ !$isEditMode ? 'required' : '' }}>
                        
                        @if($isEditMode)
                            <p class="text-xs text-gray-500 mt-1.5 font-medium">Leave this field blank to keep the user's current password.</p>
                        @endif
                        @error('password') <span class="text-red text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 mt-4 pt-4 ">
                        <button type="button" wire:click="closeModal" class="px-6 py-2 rounded-lg font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">Cancel</button>
                        <button type="submit" class="px-6 py-2 rounded-lg font-bold text-white bg-blue hover:opacity-90 shadow-md transition">
                            {{ $isEditMode ? 'Save Changes' : 'Create Account' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>