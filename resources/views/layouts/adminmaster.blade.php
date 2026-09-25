<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    @livewireStyles
</head>
<body>
    <header>
        
    </header>

    <h1>This is the master page for admins.</h1>
    
    <main>
        {{ $slot }} 
        
    </main>
    <footer class="bg-white border-t border-gray-200 mt-5 w-full">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
    
            <!-- Brand & Address -->
            <div class="flex flex-col items-center md:items-start text-center md:text-left">
                <div class="flex items-center gap-2 mb-2">
   
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#bababa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-paw-print preview-icon"><circle cx="11" cy="4" r="2"/><circle cx="18" cy="8" r="2"/><circle cx="20" cy="16" r="2"/><path d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q6.52 17.48 4.46 16.84A3.5 3.5 0 0 1 5.5 10Z"/></svg>
                    <span class="text-xl font-extrabold text-gray-400">Doc Jay's Veterinary Clinic</span>
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