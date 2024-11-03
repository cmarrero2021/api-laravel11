<x-mail::message>
# Bienvenido al Sistema de Registro de APIs en Laravel 11
Estimado {{ $mailData['nombre'] }}
Bienvenido  al Sistema de Registro de APIs en Laravel 11; ahora sólo necesita verificar su correo electrónico presionando el botón de abajo para poder acceder al sistema


<x-mail::button :url="$mailData['url']">
Botón
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
