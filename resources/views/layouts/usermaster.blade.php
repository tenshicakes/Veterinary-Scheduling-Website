<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - Doc Jay's Vet Clinic</title>
    @vite('resources/css/app.css') 
    @livewireStyles
</head>
<!-- x-data="{ sidebarOpen: false }" built-in function to track if the menu is open or closed -->
<body class="bg-background text-gray-800 font-sans" x-data="{ sidebarOpen: false }">

    <!-- THE BOOKMARK BURGER BUTTON -->
    <!-- This floats on the top left. Clicking it sets sidebarOpen to true -->
    <button @click="sidebarOpen = true" class="fixed top-6 left-0 z-40 bg-primary text-surface p-3 rounded-r-xl shadow-lg hover:bg-blue transition">
        <!-- Lucide Menu Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>
        </svg>
    </button>

    <!-- DARK OVERLAY BACKGROUND -->
    <!-- Closes the menu if the user clicks outside of the sidebar -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak style="display: none;" class="fixed inset-0 bg-gray-900/40 z-40 transition-opacity"></div>

    <!-- THE SIDEBAR PANEL -->
    <!-- Translates smoothly in and out of the left edge based on sidebarOpen -->
    <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-[8px_0_30px_rgba(0,0,0,0.12)] transform transition-transform duration-300 flex flex-col">
        
        <!-- TOP: Logo and Close Button -->
        <div class="p-6 flex justify-between items-center border-b border-gray-100">
            <img src="{{ asset('images/clinic-logo.png') }}" alt="Clinic Logo" class="h-12 w-auto object-contain">
            
            <button @click="sidebarOpen = false" class="text-gray-400 hover:text-red-500 transition">
                <!-- Lucide X Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <!-- MIDDLE: Navigation Links -->
        <!-- flex-1 ensures this section takes up all remaining space, pushing the logout button to the bottom -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            
            <a href="{{ route('user.home') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-lg text-primary bg-blue-50 hover:bg-blue hover:text-white transition">
                <!-- Lucide Home Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Home
            </a>

            <a href="{{ route('user.appointment') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-lg text-primary hover:bg-blue hover:text-white transition">
                <!-- Lucide Calendar Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>
                </svg>
                Book Appointment
            </a>

            <a href="{{ route('user.profile') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-lg text-primary hover:bg-blue hover:text-white transition">
                <!-- Lucide User Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                Profile
            </a>
        </nav>

        <!-- BOTTOM: Logout Button -->
        <div class="p-6 border-t border-gray-100">
            <!-- We use a form with @csrf for security -->
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="flex items-center justify-center gap-2 w-full bg-red-50 text-red font-bold py-3 rounded-lg hover:bg-red hover:text-white transition">
                    <!-- Lucide LogOut Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>
                    </svg>
                    Log Out
                </button>
            </form>
        </div>
    </div>

    <!-- MAIN CONTENT AREA -->
    <!-- The padding-left prevents the content from hiding behind the bookmark button -->
    <main class="min-h-screen pl-16 md:pl-24 p-8">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>