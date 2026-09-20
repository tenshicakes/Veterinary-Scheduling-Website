<!-- LEFT SIDE: CALENDAR -->
<div class="w-full md:w-1/2 bg-white border border-gray-200 rounded-xl p-4 md:p-6 shadow-sm">
    <h4 class="font-bold text-lg text-primary text-center mb-4">{{ $currentMonthName }}</h4>
    
    <!-- Days of the Week Header -->
    <div class="grid grid-cols-7 gap-1 text-center text-xs font-bold text-gray-400 mb-2">
        <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
    </div>

    <!-- Calendar Grid -->
    <div class="grid grid-cols-7 gap-1 text-center">
        <!-- Empty slots to align the first day correctly -->
        @for ($i = 0; $i < $firstDayOfWeek; $i++)
            <div class="py-2 px-1 sm:p-2"></div>
        @endfor

        <!-- Actual Days -->
        @for ($day = 1; $day <= $daysInMonth; $day++)
            @php
                $dateString = \Carbon\Carbon::now()->format('Y-m-') . str_pad($day, 2, '0', STR_PAD_LEFT);
                $isPast = \Carbon\Carbon::parse($dateString)->isBefore($today);
                $isUnavailable = in_array($dateString, $unavailableDates) || $isPast;
            @endphp

            @if($isUnavailable)
                <!-- Grayed out unavailable day -->
                <div class="py-2 px-1 sm:p-2 text-sm font-medium text-gray-300 bg-gray-50 rounded cursor-not-allowed">
                    {{ $day }}
                </div>
            @else
                <!-- Clickable available day -->
                <div wire:click="selectDate('{{ $dateString }}')" 
                     class="py-2 px-1 sm:p-2 text-sm font-bold rounded cursor-pointer transition hover:bg-blue-100 
                     {{ $selectedDate === $dateString ? 'bg-blue text-white shadow-md' : 'text-gray-700 bg-white border border-gray-100' }}">
                    {{ $day }}
                </div>
            @endif
        @endfor
    </div>
</div>