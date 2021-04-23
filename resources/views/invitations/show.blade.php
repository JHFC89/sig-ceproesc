<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Email')" />

                <x-input :value="$invitation->email" readonly id="email" class="block mt-1 w-full bg-gray-100" type="email" name="email" required />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" value="Senha" />

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                autofocus
                                required />
            </div>

            <!-- Password Confirmation -->
            <div class="mt-4">
                <x-label for="password_confirmation" value="Confirmar Senha" />

                <x-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation"
                                required />
            </div>

            <!-- Invitation -->
            <x-input class="block mt-1 w-full"
                            type="hidden"
                            name="confirmation_code"
                            :value="$invitation->code"
                            required />

            <div class="block mt-4">
                <div class="flex items-center">
                    <span class="ml-2 text-sm text-gray-600">A senha deve ter pelo menos 6 caracteres e ao menos uma letra maiúscula, minúscula e um número.</span>
                </div>
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button class="ml-3">
                    Cadastrar
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
