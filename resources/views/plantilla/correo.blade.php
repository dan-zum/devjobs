<x-app-layout>
    <div class="py-16 bg-gray-50 overflow-hidden lg:py-24">
        <div class=" max-w-xl mx-auto px-4 sm:px-6 lg:px-8 lg:max-w-7xl">
            <div class="relative">
                <h2 class="text-center text-4xl leading-8 font-extrabold tracking-tight
                 text-indigo-600 sm:text-6xl">Hola @isset($nombre) {{ $nombre }} @endisset Creemos que te puede interesar esta Vacante</h2>
                <p class="mt-4 max-w-3xl mx-auto text-center text-xl text-gray-500">Hay una nueva vacante de  <a href="">@isset($detalle) {{ $detalle }} @endisset</a></p>
            </div>
        </div>
    </div>
</x-app-layout>
