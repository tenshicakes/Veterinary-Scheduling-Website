@props(['services', 'selectedService' => null])

<div class="overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="p-3 sm:p-4 font-bold text-gray-600 text-sm">Service Name</th>
                <th class="p-3 sm:p-4 font-bold text-gray-600 text-sm w-32">Price</th>
            </tr>
        </thead>
        <tbody>
            @forelse($services as $service)
                <!-- wire:click allows the parent component to capture the click instantly -->
                <tr wire:click="$set('selectedService', '{{ $service->serviceID ?? $service->id }}')" 
                class="cursor-pointer border-b border-gray-200 hover:bg-blue transition 
                {{ $selectedService == ($service->serviceID ?? $service->id) ? 'bg-blue border-l-4 border-l-primary' : 'border-l-4 border-l-transparent' }}">
                    
                    <td class="p-3 sm:p-4 font-medium transition {{ $selectedService == ($service->serviceID ?? $service->id) ? 'text-white' : 'text-gray-800 group-hover:text-white' }}">
                        {{ $service->servicename }}
                    </td>
                    <td class="p-3 sm:p-4 font-bold transition {{ $selectedService == ($service->serviceID ?? $service->id) ? 'text-white' : 'text-primary group-hover:text-white' }}">
                        ₱{{ number_format($service->price, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="p-3 sm:p-4 text-center text-gray-500">No active services available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>