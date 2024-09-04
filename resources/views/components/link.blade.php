 @php
    $classes="text-sm text-gray-500 hover:text-gray-900 font-bold"
 @endphp
 <!--$attributes toma todos atributos que le paso al componente--->
 <a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>