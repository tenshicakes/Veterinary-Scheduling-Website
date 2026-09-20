<div class="min-h-screen bg-background flex flex-col justify-center items-center p-4">
    
    <!-- Clinic Branding / Logos -->
    <div class="mb-8 text-center">
        <h1 class="text-4xl font-bold text-primary mb-2">Doc Jay's Veterinary Clinic</h1>
        <p class="text-gray-600">Online Appointment Scheduling System</p>
        <img src="{{ asset('images/DogCat.png') }}" alt="DogCat" class="mx-auto mt-4 w-32 h-32">
    </div>

    <!-- The Form Container -->
    <div class="w-full max-w-md bg-surface rounded-xl shadow-lg p-8">
        
        @if($isLogin)
            <!-- LOGIN FORM -->
            <h2 class="text-2xl font-bold text-primary mb-6">Welcome Back</h2>
            <form wire:submit="authenticate" class="flex flex-col gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" wire:model="email" class="mt-1 w-full border rounded p-2 focus:border-primary" required>
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" wire:model="password" class="mt-1 w-full border rounded p-2 focus:border-primary" required>
                </div>
                <button type="submit" class="w-full bg-primary text-surface font-semibold py-2 rounded mt-2">Log In</button>
            </form>
            <p class="mt-4 text-sm text-center text-gray-600">
                New here? <button wire:click="toggleForm" class="text-secondary font-semibold hover:underline">Sign up</button>
            </p>

        @else
            <!-- REGISTER FORM -->
            <h2 class="text-2xl font-bold text-primary mb-6">Create an Account</h2>
            <form wire:submit="register" class="flex flex-col gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" wire:model="fullname" class="mt-1 w-full border rounded p-2 focus:border-primary" required>
                    @error('fullname') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" wire:model="email" class="mt-1 w-full border rounded p-2 focus:border-primary" required>
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" wire:model="password" class="mt-1 w-full border rounded p-2 focus:border-primary" required>
                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="w-full bg-secondary text-surface font-semibold py-2 rounded mt-2">Sign Up</button>
            </form>
            <p class="mt-4 text-sm text-center text-gray-600">
                Already have an account? <button wire:click="toggleForm" class="text-primary font-semibold hover:underline">Log in</button>
            </p>
        @endif

    </div>
</div>