<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistant Dashboard - Doc Jay's Vet Clinic</title>
    @vite('resources/css/app.css') 
    @livewireStyles
</head>

<body class="bg-background text-gray-800 font-sans" x-data="{ sidebarOpen: false }">

    <!-- BOOKMARK BURGER BUTTON -->
    <button @click="sidebarOpen = true" class="fixed top-6 left-0 z-40 bg-primary text-surface p-3 rounded-r-xl shadow-lg hover:bg-blue transition">
        <!-- Lucide Menu Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>
        </svg>
    </button>

    <!-- Closes the menu  -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak style="display: none;" class="fixed inset-0 bg-gray-900/40 z-40 transition-opacity"></div>

    <!-- THE SIDEBAR PANEL -->
    <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-[8px_0_30px_rgba(0,0,0,0.12)] transform transition-transform duration-300 flex flex-col">
        
        <div class="p-6 flex justify-between items-center border-b border-gray-100">
            <img src="{{ asset('images/clinic-logo.png') }}" alt="Clinic Logo" class="h-12 w-auto object-contain">
            
            <button @click="sidebarOpen = false" class="text-gray-400 hover:text-red-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

<!-- Navigation Links -->
    
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            
            <a href="{{ route('assistant.home') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-lg {{ request()->routeIs('assistant.home') ? 'bg-blue-50 text-primary' : 'text-primary hover:bg-blue hover:text-white' }} transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Home
            </a>

            <a href="{{ route('assistant.appointments') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-lg {{ request()->routeIs('assistant.appointments') ? 'bg-blue-50 text-primary' : 'text-primary hover:bg-blue hover:text-white' }} transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>
                </svg>
                Appointments
            </a>

            <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-lg {{ request()->routeIs('assistant.profile') ? 'bg-blue-50 text-primary' : 'text-primary hover:bg-blue hover:text-white' }} transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                Profile
            </a>
        </nav>

        <!-- Logout Button -->
        <div class="p-6 border-t border-gray-100">
            <!-- use  @csrf for security -->
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="flex items-center justify-center gap-2 w-full bg-red-50 text-red font-bold py-3 rounded-lg hover:bg-red hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>
                    </svg>
                    Log Out
                </button>
            </form>
        </div>
    </div>


    <main class="min-h-screen pl-16 md:pl-24 p-8">
        {{ $slot }}
        
    </main>

    <footer class="bg-white border-t border-gray-200 mt-5 w-full">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
    
            <!-- Brand & Address -->
            <div class="flex flex-col items-center md:items-start text-center md:text-left">
                <div class="flex items-center gap-2 mb-2">
   
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#bababa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-paw-print preview-icon"><circle cx="11" cy="4" r="2"/><circle cx="18" cy="8" r="2"/><circle cx="20" cy="16" r="2"/><path d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q6.52 17.48 4.46 16.84A3.5 3.5 0 0 1 5.5 10Z"/></svg>
                    <span class="text-xl font-extrabold text-gray-400">Doc Jay's Vet Clinic</span>
                </div>
    
                <p class="text-sm text-gray-400 font-medium max-w-sm">
                #71 Panorama St. SSS Village, Concepcion Dos, Marikina City
                </p>
                <p class="text-sm text-gray-400 font-medium max-w-sm mt-1">
                    09672848410
                </p>
                <p class="text-sm text-gray-400 font-medium max-w-sm mt-1">
                    veterinaryservicesbydocjay@gmail.com
                </p>
            </div>

            <!-- Copyright, System Info & Developer Credit -->
            <div class="flex flex-col items-center md:items-end text-sm text-gray-400 font-medium">
                <p>&copy; {{ date('Y') }} Doc Jay's Veterinary Clinic. All rights reserved.</p>
                <p class="mt-1">Online Veterinary Appointment Scheduling System</p>
                
                <!-- Social Links -->
                <div class="flex gap-4 mt-3">
                    <a href="https://www.facebook.com/veterinaryservicesbydocjay" target="_blank" class="hover:text-blue transition">Facebook</a>
                    <a href="https://www.instagram.com/docjaysveterinaryclinic" target="_blank" class="hover:text-blue transition">Instagram</a>
                </div>

                <!-- developer credits -->
                <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-center md:justify-end gap-2 w-full md:w-auto">
                    <span class="text-xs text-gray-400">System Developed by:</span>
                    <div class="flex items-center gap-1.5">

                        <img src="{{ asset('images/devlogo.jpg') }}" alt="Programmer Logo" class="w-5 h-5 rounded-full object-cover border border-gray-200 bg-gray-50">
                        <span class="text-gray-400 font-bold text-xs tracking-wide hover:text-red transition cursor-default">Ashley Nicole Chua</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</footer>

    @livewireScripts
</body>
</html>