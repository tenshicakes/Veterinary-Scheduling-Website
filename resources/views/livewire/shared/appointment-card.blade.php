<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($appointments as $appt)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-col justify-between transition hover:shadow-md">
                
                <div>
                    <!-- Small timestamp so the Assistant knows exactly when the request was made -->
                    <div class="text-[10px] uppercase tracking-wide text-gray-500 font-bold mb-2">
                        Requested on {{ \Carbon\Carbon::parse($appt->created_at)->format('M d, Y - h:i A') }}
                    </div>

                    <!-- Card Header: Names and Status Badge -->
                    <div class="flex justify-between items-start mb-4 border-b border-gray-100 pb-3">
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg">{{ $appt->patient_name ?? 'Unknown Owner' }}</h4>
                            <p class="text-sm text-gray-500">Pet: <span class="font-bold text-gray-700">{{ $appt->pet_name ?? 'Unknown' }}</span></p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold text-white 
                            {{ $appt->status == 'Pending' ? 'bg-yellow-500' : '' }}
                            {{ $appt->status == 'Approved' ? 'bg-green-500' : '' }}
                            {{ in_array($appt->status, ['Cancelled', 'Rejected', 'No-Show']) ? 'bg-red' : '' }}
                            {{ $appt->status == 'Completed' ? 'bg-gray-500' : '' }}">
                            {{ $appt->status }}
                        </span>
                    </div>

                    <!-- Appointment Details -->
                    <div class="flex flex-col gap-2 mb-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Appt Date:</span>
                            <span class="font-bold text-gray-800 text-right">
                                {{ \Carbon\Carbon::parse($appt->appointmentdate)->format('M d, Y') }}<br>
                                {{ \Carbon\Carbon::parse($appt->appointmenttime)->format('h:i A') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Service:</span>
                            <span class="font-bold text-gray-800 text-right">{{ $appt->service_name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total:</span>
                            <span class="font-bold text-green-600">₱{{ number_format($appt->service_price ?? 0, 2) }}</span>
                        </div>
                    </div>

                    <!-- Payment / Extra Notes -->
                    @if($appt->notes)
                        <div class="bg-gray-50 rounded border border-gray-200 p-3 mb-4 text-xs text-gray-700 wrap-break-word max-h-24 overflow-y-auto">
                            <strong>Notes / Payment:</strong><br>
                            {{ $appt->notes }}
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100">
                    @if($appt->status == 'Pending')
                        <!-- Added wire:confirm to verify payment before approving -->
                        <button wire:confirm="Approve this appointment? Ensure you have verified the payment notes." 
                                wire:click="updateStatus({{ $appt->appointmentID }}, 'Approved')" 
                                class="flex-1 min-w-[80px] bg-primary text-surface font-bold py-1.5 px-2 rounded shadow-sm hover:bg-blue-800 transition text-xs">Approve</button>
                        
                        <button wire:click="openRescheduleModal({{ $appt->appointmentID }}, '{{ $appt->patient_name ?? 'Unknown Owner' }}')" 
                                class="flex-1 min-w-[80px] bg-blue-400 text-white font-bold py-1.5 px-2 rounded shadow-sm hover:bg-blue-500 transition text-xs">Reschedule</button>
                        
                        <!-- Added wire:confirm to prevent accidental rejections -->
                        <button wire:confirm="Are you sure you want to REJECT this pending request?" 
                                wire:click="updateStatus({{ $appt->appointmentID }}, 'Rejected')" 
                                class="flex-1 min-w-[80px] bg-red-500 text-white font-bold py-1.5 px-2 rounded shadow-sm hover:bg-red-600 transition text-xs">Reject</button>
                    
                    @elseif($appt->status == 'Approved')
                        <!-- confirm to safely mark as completed -->
                        <button wire:confirm="Mark this appointment as Completed?" 
                                wire:click="updateStatus({{ $appt->appointmentID }}, 'Completed')" 
                                class="flex-1 min-w-[75px] bg-gray-800 text-white font-bold py-1.5 px-2 rounded shadow-sm hover:bg-gray-900 transition text-xs">Complete</button>
                        
                        <!-- confirm to verify No-Shows -->
                        <button wire:confirm="Are you sure this patient is a No-Show? This will move them to History." 
                                wire:click="updateStatus({{ $appt->appointmentID }}, 'No-Show')" 
                                class="flex-1 min-w-[75px] bg-orange-500 text-white font-bold py-1.5 px-2 rounded shadow-sm hover:bg-orange-600 transition text-xs">No-Show</button>
                        
                        <button wire:click="openRescheduleModal({{ $appt->appointmentID }}, '{{ $appt->patient_name ?? 'Unknown Owner' }}')" 
                                class="flex-1 min-w-[75px] bg-blue-400 text-white font-bold py-1.5 px-2 rounded shadow-sm hover:bg-blue-500 transition text-xs">Reschedule</button>
                        
                        <!-- confirm to prevent accidental cancellations -->
                        <button wire:confirm="Are you sure you want to CANCEL this approved appointment?" 
                                wire:click="updateStatus({{ $appt->appointmentID }}, 'Cancelled')" 
                                class="flex-1 min-w-[75px] bg-red-500 text-white font-bold py-1.5 px-2 rounded shadow-sm hover:bg-red-600 transition text-xs">Cancel</button>
                    @endif
                </div>
                
            </div>
        @empty
            <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-white p-8 rounded-xl border border-dashed border-gray-300 text-center text-gray-500">
                No appointments found for this tab or search query.
            </div>
        @endforelse
    </div>