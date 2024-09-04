<x-guest-layout>
    <!--No validate para que aparezcan las validaciones-->
    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('¿Que tipo de cuenta deseas en DevJobs?')" />
            <select
                id="rol"
                name="rol"
                class="block mt-1 w-full"
                required
             >
                <option value="">--  Selecciona un rol ---</option>
                <option value="1">Developer -- Obtener Empleo</option>
                <option value="2">Reclutador  --Publicar Empleos</option>
            </select>
             <x-input-error :messages="$errors->get('rol')" class="mt-2" />
        </div>
        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex justify-between my-5">
                <!--los atributos se los mandamos a la variable de $attribute de link.blade.php--->
               <x-link
                :href="route('login')"
               >
                    Iniciar sesion
               </x-link>

               <x-link
                    :href="route('password.request')"
               >
                    Olvidaste tu password?
               </x-link>
            
        </div>
        <x-primary-button class="w-full justify-center">
            {{ __('Register') }}
        </x-primary-button>
    </form>
</x-guest-layout>
