<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@elseif (trim($slot) === 'SAKU')
<img src="https://i.postimg.cc/fyLPs42c/saku-1.png" class="logo" alt="Saku Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
