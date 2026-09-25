<div class="flex flex-col-reverse md:flex-row min-h-screen">
    
<!-- Logo text and the image -->
<div class="w-full md:w-1/2 bg-primary flex flex-col pt-8 md:pt-12 relative overflow-hidden">
    
    <!-- Text Content -->
    <div class="text-left px-8 md:px-12 z-10 flex flex-col items-start">
        
        <!-- the logo itself (h-24 on mobile, h-32 on desktop) -->
        <img src="{{ asset('images/clinic-logo.png') }}" alt="Logo" class="h-24 md:h-32 w-auto object-contain mb-4">
        
        <!-- some texts -->
        <h1 class=" text-3xl md:text-5xl font-bold text-surface opacity-100 leading-tight text-blue">Trusted Care</h1>
        <h1 class="text-3xl md:text-5xl font-bold text-surface opacity-100 leading-tight text-blue">for Your <span class="text-secondary text-red"> Beloved Pets</span></h1>
        <p class=" text-surface text-lg md:text-lg opacity-80 leading-tight mt-5">We provide quality veterinary care, from routine checkups to specialized treatment, because they are family.</p>
    </div>

    <!-- the image at the bottom  -->
    <div class="mt-auto w-full flex items-end justify-center">
        <img src="{{ asset('images/DogCat.png') }}" alt="DogCat" class="w-full max-h-[40vh] md:max-h-[50vh] object-contain object-bottom">
    </div>
</div>

    <!-- Login and Sign up Forms -->
<div class="w-full md:w-1/2 bg-background flex items-center justify-center p-6 md:p-12">

    <!-- a card for the login and sign up form -->
    <div class="w-full max-w-md bg-surface rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-8 md:p-10">

        <!-- Error message -->
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm text-center font-medium shadow-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if($isLogin)
            <!-- LOGIN FORM -->
            <!-- Header with dog paw Icon -->
            <div class="mb-8">
                <div class="flex items-center gap-3">
                    <!-- Red Paw SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="text-red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-paw-print preview-icon"><circle cx="11" cy="4" r="2"/><circle cx="18" cy="8" r="2"/><circle cx="20" cy="16" r="2"/><path d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q6.52 17.48 4.46 16.84A3.5 3.5 0 0 1 5.5 10Z"/>
                    </svg>
                    <h2 class="text-3xl font-extrabold text-blue">Welcome Back!</h2>
                </div>
                <p class="text-gray-500 mt-1 ml-11 font-medium">Log in to your account</p>
            </div>

            <form wire:submit="authenticate" class="flex flex-col gap-5">
                <!-- Email  -->
                <div>
            
                    <label class="block text-sm font-bold text-blue mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <!-- Mail Icon -->
                            <svg class="w-5 h-5 text-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <input type="email" wire:model="email" class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-primary shadow-[0_4px_10px_-2px_rgba(0,0,0,0.08)]" placeholder="Enter your email" required>
                    </div>
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <!-- Password  -->
                <div>
                    <label class="block text-sm font-bold text-blue mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <!-- Lock Icon -->
                            <svg class="w-5 h-5 text-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>

                        <!-- View or not view the password ' -->
                        <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model="password" class="w-full pl-12 pr-12 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-primary shadow-[0_4px_10px_-2px_rgba(0,0,0,0.08)]" placeholder="Enter your password" required>
                    
                        <!-- eye icon to view the password typed -->
                        <button type="button" wire:click="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-primary hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29"></path></svg>
                        </button>
                    </div>
                </div>

                

                <!-- Login Button -->
                <button type="submit" class="w-full bg-red text-white font-bold py-3 rounded-lg mt-2 hover:bg-red-700 transition flex justify-center items-center gap-2 shadow-md">
                    Log In
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>
            
            <!-- dont have an account text -->
            <p class="mt-8 text-sm text-center text-gray-500 font-medium">
                Don't have an account? <button wire:click="toggleForm" class="text-red-600 font-bold hover:underline ml-1">Sign Up</button>
            </p>

        @else
            <!-- REGISTER FORM -->
            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-blue">Create an Account</h2>
                <p class="text-gray-500 mt-1 font-medium">Sign up to book your appointments</p>
            </div>

            @if (session()->has('verification_notice'))
                <div class="p-3 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg font-medium">
                    {{ session('verification_notice') }}
                </div>
            @endif

            <form wire:submit="register" class="flex flex-col gap-5">
                <div>
                    <label class="block text-sm font-bold text-blue mb-2">Full Name</label>
                    <div class="relative">
                        <input type="text" wire:model="fullname" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-primary shadow-[0_4px_10px_-2px_rgba(0,0,0,0.08)]" placeholder="Juan Dela Cruz" required>
                    </div>
                    @error('fullname') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-blue mb-2">Email Address</label>
                    <div class="relative">
                        <input type="email" wire:model="email" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-primary shadow-[0_4px_10px_-2px_rgba(0,0,0,0.08)]" placeholder="Enter your email" required>
                    </div>
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Phone Number -->
                <div>
                    <label class="block text-sm font-bold text-blue mb-2">Phone Number</label>
                    <div class="relative">
                        <input type="text" wire:model="phone_number" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-primary shadow-[0_4px_10px_-2px_rgba(0,0,0,0.08)]" placeholder="09123456789" required>
                    </div>
                    @error('phone_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Address (it is optional ) -->
                <div>
                    <label class="block text-sm font-bold text-blue mb-2">Address <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <div class="relative">
                        <input type="text" wire:model="address" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-primary shadow-[0_4px_10px_-2px_rgba(0,0,0,0.08)]" placeholder="Marikina City">
                    </div>
                    @error('address') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-primary mb-2">Password</label>
                    <div class="relative">
                        <!-- View or not view the password -->
                        <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model="password" class="w-full pl-4 pr-12 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-primary shadow-[0_4px_10px_-2px_rgba(0,0,0,0.08)]" placeholder="Create a strong password" required>
                    
                        <!-- the eye icon for sign up -->
                        <button type="button" wire:click="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-primary hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29"></path></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-red text-white font-bold py-3 rounded-lg mt-2 hover:bg-red-700 transition shadow-md">
                    Sign Up
                </button>
            </form>
            
            <p class="mt-8 text-sm text-center text-gray-500 font-medium">
                Already have an account? <button wire:click="toggleForm" class="text-blue font-bold hover:underline ml-1">Log In</button>
            </p>
        @endif

    </div>
</div>
</div>