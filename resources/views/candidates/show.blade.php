@extends('layouts.dashboard')

@push('head')
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js" integrity="sha512-YcsIPGdhPK4P/uRW6/sruonlYj+Q7UHWeKfTAkBW+g83NKM+jMJFJ4iAPfSnVp7BKD4dKMHmVSvICUbE/V1sSw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@section('title', 'Ficha Cadastral')

@section('content')

@if (session()->has('status'))
<x-alert type="success" :message="session('status')" />
@endif
@if ($errors->any())
<x-alert type="warning" message="Houve um erro ao tentar salvar o campo." />
@endif
<section class="space-y-4">

    <!-- Actions -->
    <div x-data="edit()" @edit.window="show($event.detail)" x-show="open" x-transition style="display: none !important" class="fixed inset-0 z-10 flex items-center justify-center text-left bg-black bg-opacity-50">
        <form x-ref="form" action="{{ route('candidates.update', ['entry' => $entry->id]) }}" method="POST" class="inline-block bg-white shadow-xl rounded-lg py-10 px-4 text-gray-700 lg:w-1/3">
            @csrf
            @method('PATCH')

            <div>
                <span class="block font-semibold text-center text-red-600 text-2xl">
                    Atenção!
                </span>
                <h3 class="mt-2 text-center font-semibold text-lg">
                    Esta ação irá atualizar o campo do cadastro permanentemente.
                </h3>

            </div>

            <div class="mt-4">

                <template x-if="entry.field == 'esta_empregado'">
                    <label for="value">
                        <span x-text="entry.field" class="font-bold text-base"></span>
                        <select x-bind:name="entry.field" class="form-select w-full mt-1">
                            <option value="sim">Sim</option>
                            <option value="não">Não</option>
                        </select>
                    </label>
                </template>

                <template x-if="entry.field == 'historico'">
                    <label for="value">
                        <span x-text="entry.field" class="font-bold text-base"></span>
                        <textarea x-bind:name="entry.field" class="form-textarea w-full mt-1" rows="4">{{ $entry->historico }}</textarea>
                    </label>
                </template>

            </div>

            <div class="flex justify-between mt-8">
                <button @click.prevent="hide()" type="button" class="px-4 py-1 bg-red-500 text-white text-sm uppercase font-semibold rounded-md shadow-md hover:bg-red-700">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-1 bg-blue-500 text-white text-sm uppercase font-semibold rounded-md shadow-md hover:bg-blue-700">
                    Confirmar
                </button>
            </div>
        </form>
    </div>

    <!-- Actions -->
    <div class="flex justify-between">
        <button onclick="print()" type="button" class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown">
            baixar PDF
        </button>
        <form x-data="destroy()" x-ref="form" @keydown.window.escape="hide()" action="{{ route('candidates.destroy', ['entry' => $entry]) }}" method="POST">
            @csrf
            @method('DELETE')

            <button @click.prevent="show()" type="submit" class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-red-600 hover:bg-red-500 hover:text-blue-100 rounded-md shadown">
                deletar cadastro
            </button>

            <div x-show="open" x-transition style="display: none !important" class="fixed inset-0 z-10 flex items-center justify-center text-left bg-black bg-opacity-50">
                <div class="inline-block bg-white shadow-xl rounded-lg py-10 px-4 text-gray-700 lg:w-1/3">
                    <div>
                        <span class="block font-semibold text-center text-red-600 text-2xl">
                            Atenção!
                        </span>
                        <h3 class="mt-2 text-center font-semibold text-lg">
                            Esta ação irá apagar o cadastro permanentemente.<br>Deseja mesmo continuar?
                        </h3>
                    </div>
                    <div class="flex justify-between mt-8">
                        <button @click.prevent="hide()" type="button" class="px-4 py-1 bg-red-500 text-white text-sm uppercase font-semibold rounded-md shadow-md hover:bg-red-700">
                            Cancelar
                        </button>
                        <button @click.prevent="submit()" class="px-4 py-1 bg-blue-500 text-white text-sm uppercase font-semibold rounded-md shadow-md hover:bg-blue-700">
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <!-- Invisible PDF model  -->
    <div class="hidden">
        <div id="print" style="width: 210mm; height: 296mm;" class="relative flex overflow-hidden flex-col pt-12 bg-white">

            <section class="flex">

                <div class="flex items-center w-1/2 pl-4">
                    <x-icons.logo-ceproesc class="w-10/12 h-auto" />
                </div>

                <div class="flex justify-start items-center w-1/2 pr-12">
                    <div class="w-full space-y-2 text-center uppercase leading-none text-gray-700">
                        <h1 class="text-3xl font-bold tracking-wider">
                            {{ $entry->nome }}
                        </h1>
                        <p class="text-xl">
                            {{ \Carbon\Carbon::parse($entry->data_de_nascimento)->diff(\Carbon\Carbon::now())->format('%y anos') }}
                        </p>
                        <p class="text-xl">
                            {{ \Carbon\Carbon::parse($entry->data_de_nascimento)->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

            </section>

            <section class="flex gap-x-8 mt-10 px-14">

                <div class="w-1/2 space-y-6">

                    <article>

                        <h2 class="text-lg font-bold text-blue-800 uppercase tracking-wider">informações pessoais</h2>

                        <div class="mt-1">

                            <p class="text-xs uppercase font-bold">
                                sexo:
                                <span class="font-normal capitalize">
                                    {{ $entry->genero }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                cnh:
                                <span class="font-normal capitalize">
                                    {{ $entry->carteira_de_habilitacao }}
                                    @if ($entry->carteira_de_habilitacao == 'sim')
                                    ({{ $entry->categoria }})
                                    @endif
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                rg:
                                <span class="font-normal capitalize">
                                    {{ $entry->rg }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                cpf:
                                <span class="font-normal capitalize">
                                    {{ $entry->cpf }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                carteira de trabalho:
                                <span class="font-normal capitalize">
                                    {{ $entry->carteira_de_trabalho }}
                                    - Nº {{ $entry->numero_de_serie }}
                                </span>
                            </p>

                            @if ($entry->genero == 'masculino' && isset($entry->alistamento_militar))

                            <p class="text-xs uppercase font-bold">
                                alistamento militar:
                                <span class="font-normal capitalize">
                                    {{ $entry->alistamento_militar }}
                                </span>
                            </p>

                            @unless ($entry->alistamento_militar == 'ainda não convocado')
                            <p class="text-xs uppercase font-bold">
                                número de reservista:
                                <span class="font-normal capitalize">
                                    {{ $entry->numero_de_reservista }}
                                </span>
                            </p>
                            @endunless

                            @endif

                            <p class="text-xs uppercase font-bold">
                                título de eleitor:
                                <span class="font-normal capitalize">
                                    {{ $entry->q34 }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                escolaridade:
                                @if ($entry->escolaridade == 'ensino superior')
                                <span class="font-normal capitalize">
                                    {{ $entry->escolaridade }} -
                                    {{ optional($entry)->curso }}
                                    ({{ $entry->instituicao_de_ensino }}) -
                                    {{ $entry->situacao_escolaridade }}

                                </span>
                                @else
                                <span class="font-normal capitalize">
                                    {{ $entry->escolaridade }} -
                                    {{ $entry->situacao_escolaridade }}
                                </span>
                                @endif
                            </p>

                            <p class="text-xs uppercase font-bold">
                                conhecimentos em informática:
                                <span class="font-normal capitalize">
                                    {{ $entry->nivel_de_conhecimentos_em_informatica }}
                                    @unless ($entry->conhecimentos_em_informatica == 'nenhum')
                                    <span class="normal-case">
                                        ({{ collect(json_decode($entry->conhecimentos_em_informatica))->join(', ', ' e ') }})
                                    </span>
                                    @endunless
                                </span>
                            </p>

                            @if ($entry->possui_cursos_complementares == 'sim')
                            <p class="text-xs uppercase font-bold">
                                cursos complementares:
                                <span class="font-normal normal-case">
                                    @php
                                    $cursosArray = json_decode($entry->cursos_complementares, true);
                                    $formatedCursos = collect($cursosArray)->map(function ($curso) {
                                    return "{$curso['Nome do curso']} ({$curso['Instituição do curso']})";
                                    })->join(', ', ' e ');
                                    @endphp
                                    {{ $formatedCursos }}
                                </span>
                            </p>
                            @endif

                            <p class="text-xs uppercase font-bold">
                                habilidade manual:
                                <span class="font-normal capitalize">
                                    {{ $entry->habilidade_manual }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                naturalidade:
                                <span class="font-normal capitalize">
                                    {{ $entry->cidade_onde_nasceu }}/{{ $entry->estado_de_naturalidade }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                família recebe auxílio do governo:
                                <span class="font-normal capitalize">
                                    {{ $entry->a_familia_recebe_algum_auxilio_do_governo }}
                                </span>
                            </p>

                        </div>

                    </article>

                    <article>

                        <h2 class="text-lg font-bold text-blue-800 uppercase tracking-wider">contato</h2>

                        <div class="mt-1">

                            <p class="text-xs uppercase font-bold">
                                celular:
                                <span class="font-normal capitalize">
                                    {{ $entry->telefone }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                recado:
                                <span class="font-normal capitalize">
                                    {{ $entry->telefone_de_recado }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                e-mail:
                                <span class="font-normal normal-case">
                                    {{ $entry->email }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                endereço:
                                <span class="font-normal normal-case">
                                    {{ $entry->logradouro }}, {{ $entry->numero }}, {{ $entry->bairro }}, {{ $entry->cidade_onde_mora }} - CEP: {{ $entry->cep }} - Zona {{ $entry->zona }}
                                </span>
                            </p>

                        </div>

                    </article>

                    @if ($entry->quantas_pessoas_moram_com_voce > 0 && $entry->moradores !== null)
                    <article>

                        <h2 class="text-lg font-bold text-blue-800 uppercase tracking-wider">moradores da residência</h2>

                        <div class="mt-1 space-y-1 text-xs">
                            @foreach (json_decode($entry->moradores, true) as $morador)
                            <div>
                                <p>
                                    {{ Str::words($morador['Nome do morador'], 1, ',') }}
                                    {{ $morador['Idade do morador'] }}
                                    ({{ $morador['Parentesco'] }}):
                                    {{ $morador['Ocupação'] }}
                                </p>
                                <p>
                                    Renda: {{ $morador['Renda'] }}
                                </p>
                            </div>
                            @endforeach
                        </div>

                    </article>
                    @endif

                </div>

                <div class="w-1/2 space-y-6">

                    @if ($entry->possui_experiencia_profissional == 'sim')
                    <article>

                        <h2 class="text-lg font-bold text-blue-800 uppercase tracking-wider">histórico de trabalho</h2>

                        <div class="mt-1 space-y-1">
                            @foreach (json_decode($entry->experiencia_profissional, true) as $empresa)

                            <div>
                                <p class="text-xs uppercase font-bold">
                                    Empresa:
                                    <span class="font-normal normal-case">
                                        {{ ucfirst($empresa['Empresa']) }}
                                    </span>
                                </p>
                                <p class="text-xs uppercase font-bold">
                                    Cargo:
                                    <span class="font-normal normal-case">
                                        {{ ucfirst($empresa['Cargo']) }}
                                    </span>
                                </p>
                            </div>

                            @endforeach
                        </div>

                    </article>
                    @endif


                    <article>

                        <h2 class="text-lg font-bold text-blue-800 uppercase tracking-wider">questionamentos</h2>

                        <div class="mt-1 text-xs">

                            <p class="font-bold">
                                Quais seus principais objetivos?
                            </p>
                            <p class="font-normal">
                                {{ ucfirst($entry->quais_seus_principais_objetivos) }}
                            </p>

                            <p class="font-bold mt-2">
                                Com qual desses comportamentos você mais se identifica?
                            </p>
                            <p class="font-normal capitalize">
                                {{ ucfirst($entry->comportamento_que_se_identifica) }}
                            </p>

                            <p class="font-bold mt-2">
                                Uma música para se ouvir todos os dias
                            </p>
                            <p class="font-normal">
                                {{ ucfirst($entry->uma_musica) }}
                            </p>

                            <p class="font-bold mt-2">
                                Quando você se olha no espelho, você enxerga
                            </p>
                            <p class="font-normal">
                                {{ ucfirst(collect(json_decode($entry->no_espelho_voce_enxerga))->join(', ', ' e ')) }}
                            </p>

                            <p class="font-bold mt-2">
                                Qual profissão gostaria de ter?
                            </p>
                            <p class="font-normal">
                                {{ ucfirst($entry->qual_profissao_gostaria) }}
                            </p>

                            <p class="font-bold mt-2">
                                Expectativas com o programa jovem aprendiz/estágio
                            </p>
                            <p class="font-normal">
                                {{ ucfirst($entry->expectativas_com_o_programa) }}
                            </p>

                            <p class="font-bold mt-2">
                                Uma frase que resume sua vida
                            </p>
                            <p class="font-normal">
                                {{ ucfirst($entry->uma_frase) }}
                            </p>

                            <p class="font-bold mt-2">
                                Para você pode faltar tudo, menos...
                            </p>
                            <p class="font-normal">
                                {{ ucfirst($entry->pode_faltar_tudo_menos) }}
                            </p>

                            <p class="font-bold mt-2">
                                O que você acrescentaria na sua personalidade?
                            </p>
                            <p class="font-normal">
                                {{ ucfirst($entry->acrescentaria_na_personalidade) }}
                            </p>

                            <p class="font-bold mt-2">
                                Deixe uma mensagem para a humanidade
                            </p>
                            <p class="font-normal">
                                {{ ucfirst($entry->mensagem_para_humanidade) }}
                            </p>

                        </div>

                        <article>

                </div>

            </section>

            <footer class="mt-auto py-2 text-white bg-blue-900">
                <div class="text-center text-sm font-normal">
                    <span>Rua Expedicionários do Brasil, 2269 - Centro, Araraquara-SP | 14801-3660</span>
                </div>
                <div class="flex justify-center mt-1 space-x-8">
                    <div>
                        <span>(16) 9 9116-2756</span>
                    </div>
                    <div>
                        <span>(16) 3322-5810</span>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <!-- Visible panel -->
    <div class="lg:grid lg:grid-cols-2 lg:gap-x-8">

        <div class="space-y-8">
            <x-card.list.description-layout title="dados cadastrais">

                <x-slot name="items">

                    @foreach ($entry->getSection('dados cadastrais') as $key => $value)

                    @if ($key == 'data_de_nascimento')

                    <x-card.list.description-item :label="$entry->getTitle($key)" :description="\Carbon\Carbon::parse($value)->format('d/m/Y')" :layout="true" />

                    <x-card.list.description-item label="Idade" :description="\Carbon\Carbon::parse($value)->diff(now())->format('%y anos')" :layout="true" />

                    @continue

                    @endif

                    <x-card.list.description-item :label="$entry->getTitle($key)" :description="$value" :type="in_array($key, ['email', 'facebook', 'instagram']) ? 'text' : 'title'" :layout="true" />

                    @endforeach

                </x-slot>

            </x-card.list.description-layout>

            <x-card.list.description-layout title="dados familiares">

                <x-slot name="items">

                    @foreach ($entry->getSection('dados familiares') as $key => $value)

                    @if ($key == 'moradores' && $value !== null)

                    @foreach (json_decode($value, true) as $morador)
                    <x-card.list.description-item label="Nome do morador" :description="$morador['Nome do morador']" :layout="true" />
                    <x-card.list.description-item label="Parentesco" :description="$morador['Parentesco']" :layout="true" />
                    <x-card.list.description-item label="Idade do morador" :description="$morador['Idade do morador']" :layout="true" />
                    <x-card.list.description-item label="Ocupação" :description="$morador['Ocupação']" :layout="true" />
                    <x-card.list.description-item label="Renda" :description="$morador['Renda']" :layout="true" />
                    @endforeach

                    @continue

                    @endif

                    @if ($key != 'moradores')
                    <x-card.list.description-item :label="$entry->getTitle($key)" :description="$value" :layout="true" />
                    @endif

                    @endforeach

                </x-slot>

            </x-card.list.description-layout>

            <x-card.list.description-layout title="documentação">

                <x-slot name="items">

                    @foreach ($entry->getSection('documentação') as $key => $value)

                    @if ($value == null)
                    @continue
                    @endif

                    <x-card.list.description-item :label="$entry->getTitle($key)" :description="$value" :layout="true" />

                    @endforeach

                </x-slot>

            </x-card.list.description-layout>

        </div>

        <div class="space-y-8">

            <x-card.list.description-layout title="escolaridade">

                <x-slot name="items">

                    @foreach ($entry->getSection('escolaridade') as $key => $value)

                    @if ($value == null)
                    @continue
                    @endif

                    <x-card.list.description-item :label="$entry->getTitle($key)" :description="$value" :layout="true" />

                    @endforeach

                </x-slot>

            </x-card.list.description-layout>

            <x-card.list.description-layout title="experiência">

                <x-slot name="items">

                    <x-card.list.description-item :label="$entry->getTitle('nivel_de_conhecimentos_em_informatica')" :description="$entry->nivel_de_conhecimentos_em_informatica" :layout="true" />

                    <x-card.list.description-item :label="$entry->getTitle('conhecimentos_em_informatica')" :description="ucfirst(collect(json_decode($entry->conhecimentos_em_informatica))->join(', ', ' e '))" :layout="true" type="text" />

                    <x-card.list.description-item :label="$entry->getTitle('possui_cursos_complementares')" :description="$entry->possui_cursos_complementares" :layout="true" />

                    @if ($entry->possui_cursos_complementares == 'sim')
                    @foreach (json_decode($entry->cursos_complementares, true) as $curso)
                    <x-card.list.description-item label="Nome do curso" :description="$curso['Nome do curso']" :layout="true" />
                    <x-card.list.description-item label="Instituição do curso" :description="$curso['Instituição do curso']" :layout="true" />
                    <x-card.list.description-item label="Duração do curso" :description="$curso['Duração do curso']" :layout="true" />
                    @endforeach
                    @endif

                    <x-card.list.description-item :label="$entry->getTitle('possui_experiencia_profissional')" :description="$entry->possui_experiencia_profissional" :layout="true" />

                    @if ($entry->possui_experiencia_profissional == 'sim')
                    @foreach (json_decode($entry->experiencia_profissional, true) as $empresa)
                    <x-card.list.description-item label="Empresa" :description="$empresa['Empresa']" :layout="true" />
                    <x-card.list.description-item label="Cargo" :description="$empresa['Cargo']" :layout="true" />
                    <x-card.list.description-item label="Período" :description="$empresa['Período']" :layout="true" />
                    @endforeach
                    @endif

                    <div class="relative">
                        <x-card.list.description-item x-data="
                                {{ json_encode(['data' => ['field' => 'esta_empregado', 'value' => $entry->esta_empregado]]) }}
                            " :label="$entry->getTitle('esta_empregado')" :description="$entry->esta_empregado" :layout="true">
                            <button x-on:click.prevent="$dispatch('edit', data)" type="button" class="absolute right-0 text-gray-300 hover:text-blue-300">
                                <x-icons.edit class="w-6 h-6" />
                            </button>
                        </x-card.list.description-item>
                    </div>

                </x-slot>

            </x-card.list.description-layout>

            <x-card.list.description-layout title="sobre você">

                <x-slot name="items">

                    @foreach ($entry->getSection('sobre você') as $key => $value)

                    @if ($key == 'no_espelho_voce_enxerga')

                    @break

                    @endif

                    @if ($key == 'expectativas_com_o_programa')

                    <x-card.list.description-item label="Quais São Suas Expectativas Com O Programa Jovem Aprendiz/Estágio:" :description="$value" :type="'textarea' == 'textarea' ? 'text' : 'title'" :linebreak="'textarea' == 'textarea' ? true : false" :layout="true" />

                    @continue

                    @endif

                    <x-card.list.description-item :label="$entry->getTitle($key)" :description="ucfirst($value)" :type="'textarea' == 'textarea' ? 'text' : 'title'" :linebreak="'textarea' == 'textarea' ? true : false" :layout="true" />

                    @endforeach

                    <x-card.list.description-item :label="$entry->getTitle($key)" :description="ucfirst(collect(json_decode($entry[$key]))->join(', ', ' e '))" :layout="true" type="text" />

                    @foreach ($entry->getSection('sobre você') as $key => $value)

                    @if (in_array($key, ['quais_seus_principais_objetivos', 'expectativas_com_o_programa', 'comportamento_que_se_identifica', 'uma_frase', 'uma_musica', 'pode_faltar_tudo_menos', 'no_espelho_voce_enxerga']))

                    @continue

                    @endif

                    <x-card.list.description-item :label="$entry->getTitle($key)" :description="ucfirst($value)" :type="'textarea' == 'textarea' ? 'text' : 'title'" :linebreak="'textarea' == 'textarea' ? true : false" :layout="true" />

                    @endforeach
                </x-slot>

            </x-card.list.description-layout>

        </div>
    </div>

    <x-card.list.description-layout title="interno">

        <x-slot name="items">

            <x-card.list.description-item x-data="
                    {{ json_encode(['data' => ['field' => 'historico', 'value' => $entry->historico]]) }}
                " label="Histórico" :description="$entry->historico" type="text" :linebreak="true">
                <button x-on:click.prevent="$dispatch('edit', data)" type="button" class="text-gray-300 hover:text-blue-300">
                    <x-icons.edit class="w-6 h-6" />
                </button>
            </x-card.list.description-item>

        </x-slot>

    </x-card.list.description-layout>

</section>

@endsection

@push('footer')
<script>
    function print() {
        filename = 'ficha-{{ Str::slug($entry->nome, ' - ') }}.pdf';
        opt = {
            filename: filename,
            image: {
                type: 'jpeg',
                quality: 1
            },
        }
        html2pdf(document.getElementById('print'), opt);
    }
</script>

<script>
    function destroy() {
        return {
            open: false,

            show() {
                this.open = true
                document.body.classList.add('overflow-hidden')
            },
            hide() {
                this.open = false
                document.body.classList.remove('overflow-hidden')
            },

            submit() {
                this.$refs.form.submit()
            }
        }
    }

    function edit() {
        return {
            open: false,

            entry: {
                field: null,
                value: null
            },

            show(data) {
                this.entry.field = data.field
                this.entry.value = data.value

                this.open = true
                document.body.classList.add('overflow-hidden')
            },

            hide() {
                this.open = false
                document.body.classList.remove('overflow-hidden')
            },
        }
    }
</script>
@endpush
