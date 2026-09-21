<div class="w-full bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
    
    <!-- HEADER: Month Navigation -->
    <div class="flex items-center justify-between mb-4">
        <!-- Back Button (Disabled if viewing the current month) -->
        <button wire:click="previousMonth" 
                class="p-1 rounded-full transition {{ $monthOffset <= 0 ? 'text-gray-300 cursor-not-allowed' : 'text-primary hover:bg-blue-50' }}"
                {{ $monthOffset <= 0 ? 'disabled' : '' }}>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </button>

        <h4 class="font-bold text-lg text-primary text-center">{{ $currentMonthName }}</h4>
        
        <!-- Next Button -->
        <button wire:click="nextMonth" class="p-1 rounded-full text-primary hover:bg-blue-50 transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </button>
    </div>
    
    <!-- Days of the Week Header -->
    <div class="grid grid-cols-7 gap-1 text-center text-xs font-bold text-gray-400 mb-2">
        <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
    </div>

    <!-- Calendar Grid -->
    <div class="grid grid-cols-7 gap-1 text-center">
        <!-- Empty slots to align the first day correctly -->
        @for ($i = 0; $i < $firstDayOfWeek; $i++)
            <div class="p-2"></div>
        @endfor

        <!-- Actual Days -->
        @for ($day = 1; $day <= $daysInMonth; $day++)
            @php
                // Automatically generate the exact date string for the currently viewed month
                $dateString = $currentYearMonth . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                $isPast = \Carbon\Carbon::parse($dateString)->isBefore($today);
                $isUnavailable = in_array($dateString, $unavailableDates) || $isPast;
            @endphp

            @if($isUnavailable)
                <!-- Grayed out unavailable/past day -->
                <div class="p-2 text-sm font-medium text-gray-300 bg-gray-50 rounded cursor-not-allowed">
                    {{ $day }}
                </div>
            @else
                <!-- Clickable available day -->
                <div wire:click="selectDate('{{ $dateString }}')" 
                     class="p-2 text-sm font-bold rounded cursor-pointer transition hover:bg-blue
                     {{ $selectedDate === $dateString ? 'bg-blue text-white shadow-md' : 'text-gray-700 bg-white border border-gray-100' }}">
                    {{ $day }}
                </div>
            @endif
        @endfor
    </div>
</div>