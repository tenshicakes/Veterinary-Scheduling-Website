<div class="w-full bg-white border border-gray-200 rounded-xl p-4 md:p-6 shadow-sm flex flex-col">
                    <h4 class="font-bold text-lg text-gray-800 mb-4">Available Time Slots</h4>
                    
                    @if(!$selectedDate)
                        <div class="flex-1 flex items-center justify-center border-2 border-dashed border-gray-200 rounded-lg p-6">
                            <p class="text-gray-400 text-sm text-center">Please select a date from the calendar to view available slots.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-3">
                            @forelse($timeSlots as $slot)
                                <div wire:click="$set('selectedTime', '{{ $slot }}')" 
                                     class="cursor-pointer border rounded-lg p-3 text-center font-bold text-sm transition hover:shadow-md 
                                     {{ $selectedTime === $slot ? 'bg-blue text-white border-primary' : 'bg-surface text-gray-700 border-gray-200' }}">
                                    {{ $slot }}
                                </div>
                            @empty
                                <div class="col-span-2 text-center text-sm text-red-500 py-4 font-medium">
                                    No slots available for this date.
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
