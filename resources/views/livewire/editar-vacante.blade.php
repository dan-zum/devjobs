<form class="md:w-1/2 space-y-5" wire:submit.prevent='EditarVacante'>
        <div>
            <x-input-label for="titulo" :value="__('Titulo Vacante')" />
            <x-text-input id="titulo" class="block mt-1 w-full" type="text" wire:model="titulo" :value="old('titulo')"  placeholder="Titulo Vacante"/>
            <x-input-error :messages="$errors->get('Salario Mensual')" class="mt-2" />

            @error('titulo')
                <livewire:mostrar-alerta :message="$message" />
            @enderror

        </div>
        <div>
            <x-input-label for="salario" :value="__('Salario Mensual')" />
            <select
                id="salario"
                wire:model="salario"
                class="block mt-1 w-full"
                
             >
             <option value="">---Seleccione la Opcion</option>
             @foreach($salarios as $salario)
                <option value="{{ $salario->id }}">{{ $salario->salario }}</option>
             @endforeach
            </select>
            @error('salario')
                <livewire:mostrar-alerta :message="$message" />
            @enderror
        </div>
        <div>
            <x-input-label for="categoria" :value="__('categoria')" />
            <select
                id="categoria"
                wire:model="categoria"
                class="block mt-1 w-full"
                
             >
             <option value="">---Seleccione la Opcion</option>
             @foreach($categorias as $categoria)
                <option value="{{ $categoria->id }}">{{ $categoria->categoria }}</option>
             @endforeach
            </select>
            </select>
            @error('categoria')
                <livewire:mostrar-alerta :message="$message" />
            @enderror
        </div>
        <div>
            <x-input-label for="empresa" :value="__('Empresa')" />
            <x-text-input id="empresa" class="block mt-1 w-full" type="text" wire:model="empresa" :value="old('empresa')"  autofocus autocomplete="username" placeholder="Empresa: ej. Netflix, Uber, Shopify"/>
            <x-input-error :messages="$errors->get('Empresa')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="ultimo_dia" :value="__('Ultimo dia para postularse')" />
            <x-text-input id="ultimo_dia" class="block mt-1 w-full" type="date" wire:model="ultimo_dia" :value="old('ultimo_dia')"  autofocus autocomplete="username"/>
            <x-input-error :messages="$errors->get('Ultimo_dia')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="descripcion_puesto" :value="__('Descripcion Puesto')" />
            <textarea
                wire:model="descripcion"
                placeholder="Descripcion general del puesto, experiencia"
                class="block mt-1 w-full h-72"
            ></textarea>
        </div>
        <div>
            <x-input-label for="imagen" :value="__('Nueva Imagen')" />
            <x-text-input id="imagen" class="block mt-1 w-full" type="file" wire:model="imagen_nueva" accept='image/*'/>
             <div class='my-5'>
                @if($imagen_nueva)
                    Imagen:
                    <img src="{{ $imagen_nueva->temporaryUrl()}}">
                @endif
            </div>
            <div class='my-5 w-80'>
                <x-input-label :value="__('Imagen Actual')" />
                <img src="{{ asset('storage/vacantes' . $imagen) }}" alt="{{ 'Imagen Vacante' . $titulo }}">
            </div>
               @error('imagen_nueva')
                    <livewire:mostrar-alerta :message="$message" />
                @enderror
        </div>
        <br>
        <x-primary-button>
            Editar Vacantes
        </x-primary-button>
</form>
