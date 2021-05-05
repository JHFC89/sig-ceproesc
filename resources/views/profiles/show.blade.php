@extends('layouts.dashboard')

@section('title', 'Informações Pessoais')

@section('content')

    @if (session()->has('status'))
    <x-alert 
        type="success" 
        message="{{ session('status') }}" 
    />
    @endif

    @error ('*')
    <x-alert 
        type="warning" 
        message="Ocorreu um erro!" 
    />
    <div class="space-y-1">
        @foreach ($errors->all() as $error)
        <x-alert 
            type="warning" 
            :message="$error" 
        />
        @endforeach
    </div>
    @enderror

    <x-card.form-layout 
        title="informações da conta" 
        :action="route('account-profiles.update', ['user' => $user])"
        method="PATCH"
    >

        <x-slot name="inputs">

            <x-card.form-input name="email" label="e-mail">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('email') border-red-500 @enderror" 
                        type="email"
                        name="email" 
                        value="{{ old('email', $user->email) }}"
                        placeholder="Digite seu email"
                    >
                    @error('email')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="password" label="nova senha">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('password') border-red-500 @enderror" 
                        type="password"
                        name="password" 
                        placeholder="Digite sua nova senha"
                    >
                    <span class="ml-2 text-sm text-gray-600 normal-case">A senha deve ter pelo menos 6 caracteres e ao menos uma letra maiúscula, minúscula e um número.</span>
                    @error('password')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="password_confirmation" label="confirmar nova senha">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('password_confirmation') border-red-500 @enderror" 
                        type="password"
                        name="password_confirmation" 
                        placeholder="Confirme sua nova senha"
                    >
                    @error('password_confirmation')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="current_password" label="senha atual">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('current_passwordword') border-red-500 @enderror" 
                        type="password"
                        name="current_password" 
                        placeholder="Digite sua senha atual"
                    >
                    @error('current_password')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

        </x-slot>

        <x-slot name="footer">
            <button 
                type="submit"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            >
                atualizar
            </button>
        </x-slot>

    </x-card.form-layout>



    <x-card.form-layout 
        title="informações pessoais" 
        :action="route('personal-profiles.update', ['user' => $user])"
        method="PATCH"
    >

        <x-slot name="inputs">

            <x-card.form-input name="name" label="nome">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('name') border-red-500 @enderror" 
                        name="name" 
                        value="{{ old('name', $user->name) }}"
                        placeholder="Digite seu nome"
                    >
                    @error('name')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            @if ($user->registration->birthdate !== null)
            <x-card.form-input name="birthdate" label="data de nascimento">
                <x-slot name="input">
                    <x-card.select-date
                        dayName="birthdate[day]"
                        :dayValue="old('birthdate.day', $user->registration->birthdate->day)"
                        monthName="birthdate[month]"
                        :monthValue="old('birthdate.month', $user->registration->birthdate->month)"
                        yearName="birthdate[year]"
                        :yearValue="old('birthdate.year', $user->registration->birthdate->year)"
                        :minYear="false"
                    />
                    @error('birthdate')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                    @error('birthdate.day')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                    @error('birthdate.month')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                    @error('birthdate.year')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>
            @endif

            @if ($user->registration->rg !== null)
            <x-card.form-input name="rg" label="RG">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('rg') border-red-500 @enderror" 
                        name="rg" 
                        value="{{ old('rg', $user->registration->rg) }}"
                        placeholder="Digite seu RG"
                    >
                    @error('rg')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>
            @endif

            @if ($user->registration->cpf !== null)
            <x-card.form-input name="cpf" label="CPF">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('cpf') border-red-500 @enderror" 
                        name="cpf" 
                        value="{{ old('cpf', $user->registration->cpf) }}"
                        placeholder="Digite o CPF do aprendiz (123.123.123-12)"
                    >
                    @error('cpf')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>
            @endif

            @if ($user->registration->responsable_name !== null)
            <x-card.form-input name="responsable_name" label="nome do responsável">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('responsable_name') border-red-500 @enderror" 
                        name="responsable_name" 
                        value="{{ old('responsable_name', $user->registration->responsable_name) }}"
                        placeholder="Digite o nome do seu responsável"
                    >
                    @error('responsable_name')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="responsable_cpf" label="CPF do responsável">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('responsable_cpf') border-red-500 @enderror" 
                        name="responsable_cpf" 
                        value="{{ old('responsable_cpf', $user->registration->responsable_cpf) }}"
                        placeholder="Digite o CPF do seu responsável (123.123.123-12)"
                    >
                    @error('responsable_cpf')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>
            @endif

        </x-slot>

        <x-slot name="footer">
            <button 
                type="submit"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            >
                atualizar
            </button>
        </x-slot>

    </x-card.form-layout>

    @if ($user->isInstructor() || $user->isEmployer() || $user->isNovice())
    <x-card.form-layout 
        title="informações profissionais" 
        :action="route('professional-profiles.update', ['user' => $user])"
        method="PATCH"
    >

        <x-slot name="inputs">

            @if ($user->isNovice() && $user->class !== null)
            <x-card.form-input name="turma" label="turma">
                <x-slot name="input">
                    <input 
                        disabled
                        class="block w-full form-textarea bg-gray-100" 
                        value="{{ $user->class }}"
                    >
                </x-slot>
            </x-card.form-input>
            @endif

            @if ($user->registration->company !== null)
            <x-card.form-input name="company" label="empresa">
                <x-slot name="input">
                    <input 
                        disabled
                        class="block w-full form-textarea bg-gray-100" 
                        value="{{ $user->registration->company->name }}"
                    >
                </x-slot>
            </x-card.form-input>
            @endif

            @if ($user->registration->employer !== null)
            <x-card.form-input name="company" label="empregador">
                <x-slot name="input">
                    <input 
                        disabled
                        class="block w-full form-textarea bg-gray-100" 
                        value="{{ $user->registration->employer->name }}"
                    >
                </x-slot>
            </x-card.form-input>
            @endif

            @if ($user->registration->ctps !== null)
            <x-card.form-input name="ctps" label="CTPS">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('ctps') border-red-500 @enderror" 
                        name="ctps" 
                        value="{{ old('ctps', $user->registration->ctps) }}"
                        placeholder="Digite o CTPS"
                    >
                    @error('ctps')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>
            @endif

        </x-slot>

        <x-slot name="footer">
            @if ($user->isInstructor() || $user->isNovice())
            <button 
                type="submit"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            >
                atualizar
            </button>
            @else
            <a 
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
                href="{{ route('companies.show', ['company' => $user->registration->company]) }}"
            >
                ver empresa
            </a>
            @endif
        </x-slot>

    </x-card.form-layout>
    @endif

    @if ($user->registration->phone !== null || $user->registration->address !== null)
    <x-card.form-layout 
        title="informações complementares" 
        :action="route('complementary-profiles.update', ['user' => $user])"
        method="PATCH"
    >

        <x-slot name="inputs">

            @if ($user->registration->phones->count() > 0)
            <x-card.form-input name="phone" label="Telefone">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('phone') border-red-500 @enderror" 
                        name="phone" 
                        value="{{ old('phone', $user->registration->phones->first()->number) }}"
                        placeholder="Digite o telefone"
                    >
                    @error('phone')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>
            @endif

            @if ($user->registration->address !== null)

            <x-card.form-input name="address.street" label="logradouro">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('street') border-red-500 @enderror" 
                        name="address[street]" 
                        value="{{ old('address.street', $user->registration->address->street) }}"
                        placeholder="Digite o logradouro do endereço do aprendiz"
                    >
                    @error('address.street')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="address.number" label="número">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('address.number') border-red-500 @enderror" 
                        name="address[number]" 
                        value="{{ old('address.number', $user->registration->address->number) }}"
                        placeholder="Digite o número do endereço do aprendiz"
                    >
                    @error('address.number')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="address.district" label="bairro">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('address.district') border-red-500 @enderror" 
                        name="address[district]" 
                        value="{{ old('address.district', $user->registration->address->district) }}"
                        placeholder="Digite o bairro do endereço do aprendiz"
                    >
                    @error('address.district')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="address.city" label="cidade">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('address.city') border-red-500 @enderror" 
                        name="address[city]" 
                        value="{{ old('address.city', $user->registration->address->city) }}"
                        placeholder="Digite a cidade do endereço do aprendiz"
                    >
                    @error('address.city')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="address.cep" label="CEP">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('address.cep') border-red-500 @enderror" 
                        name="address[cep]" 
                        value="{{ old('address.cep', $user->registration->address->cep) }}"
                        placeholder="Digite o CEP do endereço do aprendiz (12.123-123)"
                    >
                    @error('address.cep')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="address.state" label="estado">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('address.state') border-red-500 @enderror" 
                        name="address[state]" 
                        value="{{ old('address.state', $user->registration->address->state) }}"
                        placeholder="Digite o estado do endereço do aprendiz"
                    >
                    @error('address.state')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            <x-card.form-input name="address.country" label="país">
                <x-slot name="input">
                    <input 
                        class="block w-full form-textarea @error('address.country') border-red-500 @enderror" 
                        name="address[country]" 
                        value="{{ old('address.country', $user->registration->address->country) }}"
                        placeholder="Digite o país do endereço do aprendiz"
                    >
                    @error('address.country')
                        <span class="block text-sm text-red-500 normal-case">{{ $message }}</span>
                    @enderror
                </x-slot>
            </x-card.form-input>

            @endif

        </x-slot>

        <x-slot name="footer">
            <button 
                type="submit"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
            >
                atualizar
            </button>
        </x-slot>

    </x-card.form-layout>
    @endif

@endsection
