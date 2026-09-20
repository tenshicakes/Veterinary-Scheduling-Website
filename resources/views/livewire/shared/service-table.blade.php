@props(['services', 'selectedService' => null])

<div class="overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="p-4 font-bold text-gray-600 text-sm">Service Name</th>
                <th class="p-4 font-bold text-gray-600 text-sm w-32">Price</th>
            </tr>
        </thead>
        <tbody>
            @forelse($services as $service)
                <!-- wire:click allows the parent component to capture the click instantly -->
                <tr wire:click="$set('selectedService', '{{ $service->id }}')" 
                    class="cursor-pointer border-b border-gray-100 hover:bg-blue-50 transition 
                    {{ $selectedService == $service->id ? 'bg-blue-50 border-l-4 border-l-primary' : 'border-l-4 border-l-transparent' }}">
                    
                    <td class="p-4 font-medium text-gray-800">{{ $service->servicename }}</td>
                    <td class="p-4 font-bold text-primary">₱{{ number_format($service->price, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="p-4 text-center text-gray-500">No active services available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>