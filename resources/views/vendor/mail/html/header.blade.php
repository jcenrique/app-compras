@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
{{-- @if (trim($slot) === 'Laravel') --}}
<div>
<img src="https://softren.eu/images/logo.png" class="logo" alt="Logotipo de la empresa, fondo claro, estilo moderno" width="250">
</div>
{{-- @else --}}

<div>
{{ $slot }}
</div>


</a>
</td>
</tr>
