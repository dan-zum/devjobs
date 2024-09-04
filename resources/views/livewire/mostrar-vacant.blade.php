<div class="p-10">
    <div class="mb-5">
        <h1 class="font-bold text-3xl text-gray-800 my-3">{{ $vacante->titulo }}</h1>
        <div class="md:grid md:grid-cols-2 bg-gray-50 p-4">
            <p class="font-bold text-sm uppercase text-gray-800 my-3">Empresa:
                <span class="normal-case font-normal">{{ $vacante->empresa }}</span>                
            </p>
            <p class="font-bold text-sm uppercase text-gray-800 my-3">Ultimo dia para postularse:
                <span class="normal-case font-normal">{{ $vacante->ultimo_dia->toFormattedDateString() }}</span>                
            </p>
            <p class="font-bold text-sm uppercase text-gray-800 my-3">Categoria:
                <span class="normal-case font-normal">{{ $vacante->categoria->categoria }}</span>                
            </p>
            <p class="font-bold text-sm uppercase text-gray-800 my-3">Salario:
                <span class="normal-case font-normal">{{ $vacante->salario->salario }}</span>                
            </p>
        </div>
    </div>
    <div class='md:grid md:grid-cols-6 gap-4'>
        <div class='md:col-span-2'>
            <img src="{{asset('storage/vacantes' . $vacante->imagen)}}" alt="{{'Imagen Vacante' . $vacante->titulo}}">
        </div>
        <div class='md:col-span-4'>
            <h2 class='text-2xl font-bold mb-5'>Descripcion del Puesto</h2>
            <p>{{$vacante->descripcion}}</p>
        </div>
    </div>
    <!--El emtodo guest se utiliza para que accedan solo los usuarios que no estas registrados o logueados(que sean invitados)--> 
    @guest
        <div class='mt-5 bg-gray-50 border border-dashed p-5 text-center'>
            <p>
                ¿Deseas aplicar a esta vacante? <a class='font-bold text-indigo-600' href='{{ route('register') }}'>Obten una cuenta y aplica a esta y otras vacantes</a>
            </p>
        </div>
    @endguest
   <!--Comprobamos si el candidato ya aplico a esta vacante--->
   
    @cannot('create', App\Models\Vacante::class) <!---se utiliza para verificar si un usuario no tiene permiso para realizar una determinada acción-->
        <livewire:postular-vacante :vacante='$vacante'/>
    @endcannot
  
    
</div>
