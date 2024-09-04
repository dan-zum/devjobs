<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex justify-between my-5">
                <!--los atributos se los mandamos a la variable de $attribute de link.blade.php--->
               <x-link
                :href="route('login')"
               >
                    Iniciar sesion
               </x-link>

               <x-link
                    :href="route('register')"
               >
                    Registrate
               </x-link>
            
        </div>
        <x-primary-button class="w-full justify-center">
                {{ __('Enviar') }}
        </x-primary-button>
    </form>
</x-guest-layout>
