<div class="w-full bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
    
    <!-- Month Header (Static - No Navigation) -->
    <div class="flex items-center justify-between mb-4">
        <div class="w-10"></div>
        
        <h4 class="font-bold text-lg text-primary text-center">{{ $currentMonthName }}</h4>
        
        <div class="w-10"></div>
    </div>
    
    <!-- Days of the Week  -->
    <div class="grid grid-cols-7 gap-1 text-center text-xs font-bold text-gray-400 mb-2">
        <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
    </div>

    <!-- Calendar box -->
    <div class="grid grid-cols-7 gap-1 text-center">

        @for ($i = 0; $i < $firstDayOfWeek; $i++)
            <div class="p-2"></div>
        @endfor

        <!-- Actual Days -->
        @for ($day = 1; $day <= $daysInMonth; $day++)
            @php
                // Automatically generate the exact date string for the currently viewed month
                $dateString = $currentYearMonth . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                $isAvailable = in_array($dateString, $availableDates);
                $isUnavailable = !$isAvailable || in_array($dateString, $unavailableDates);
            @endphp

            @if($isUnavailable)
                <!-- Unavailable day (not in available dates or fully booked) -->
                <div class="p-2 text-sm font-medium text-gray-300 bg-gray-50 rounded cursor-not-allowed">
                    {{ $day }}
                </div>
            @else
                <!-- Available day (one of the 3 booking dates) -->
                <button type="button" 
                        wire:click="selectDate('{{ $dateString }}')" 
                        class="w-full p-2 text-sm font-bold rounded cursor-pointer transition hover:bg-blue hover:text-white
                        {{ $selectedDate === $dateString ? 'bg-blue text-white shadow-md' : 'text-gray-700 bg-white border border-gray-100' }}">
                    {{ $day }}
                </button>
            @endif
        @endfor
    </div>
</div>