@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ asset('images/DogCat.png') }}" class="logo" alt="Doc Jay's Veterinary Clinic">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
