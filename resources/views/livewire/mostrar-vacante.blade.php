<div>
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">   
        @forelse($vacantes as $vacante)
            <div class="p-6 bg-white border-b border-gray-200 md:flex md:justify-between md:items-center">
                <div class='leading-10'>
                    <a href="{{ route('vacantes.show', $vacante->id)}}" class='text-xl font-bold'>
                        {{$vacante->titulo}}
                    </a>
                    <p class='text-sm text-gary-600 font-bold'>{{ $vacante->empresa}}</p>
                    <p class='text-sm text-gray-500'>Ultimo dia: {{ $vacante->ultimo_dia->format('d/m/Y')}}</p>
                </div>
                    <div class='flex flex-col md:flex-row items-stretch gap-3 mt-5 md:mt-0'>
                        <a href='{{ route('postulaciones', $vacante->id)}}' class='bg-slate-800 text-white py-2 px-4 rounded-lg text-xs font-bold uppercase text-center'>
                        {{ Count($vacante->candidatos) }}   Candidatos
                        </a>
                        <a href='{{ route('vacantes.edit', $vacante->id)}}' class='bg-blue-800 text-white py-2 px-4 rounded-lg text-xs font-bold uppercase text-center'>
                            Editar
                        </a><!--Se agrega un alias para el evetno click-->
                        <button 
                            wire:click="$dispatch('MostrarAlerta', { id: {{  $vacante->id }} })" 
                            class='bg-red-600 text-white py-2 px-4 rounded-lg text-xs font-bold uppercase text-center'>
                            Eliminar
                        </button>
                    </div>
            </div>
    @empty
        <p class='p-3 text-center text-sm text-gray-600'> No hay Vacantes para mostrar</p>
    @endforelse   
</div>
    <div class='justify-center mt-10'>
        {{ $vacantes->links() }}
    </div> 
</div>

@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>    
        document.addEventListener('livewire:init', () =>{
            Livewire.on('MostrarAlerta', id => {
                Swal.fire({
                title: "Eliminar Vacante?",
                text: "Una vacante eliminada no se puede recuperar",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, !Eliminar!",
                cancelButtonText: 'Cancelar'
                }).then((result) => {
                if (result.isConfirmed) {
                    //Eliminar el vacante
                    @this.call('eliminarVacante',id);
                    Swal.fire({
                    title: "Deleted!",
                    text: "Your file has been deleted.",
                    icon: "success"
                    });
                }
                });
            })
        })
       
    </script>

@endpush