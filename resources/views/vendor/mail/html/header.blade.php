@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{asset('img/logo/logo-dark-je-sm.png')}}" alt="Portal JE logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
