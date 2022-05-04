@extends('layouts.dashboard')

@push('head')
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js" integrity="sha512-YcsIPGdhPK4P/uRW6/sruonlYj+Q7UHWeKfTAkBW+g83NKM+jMJFJ4iAPfSnVp7BKD4dKMHmVSvICUbE/V1sSw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@section('title', 'Ficha Cadastral')

@section('content')

@if (session()->has('status'))
    <x-alert type="success" :message="session('status')"/>
@endif


<section class="space-y-4">

    <!-- Actions -->
    <div
        x-data="edit()"
        @edit.window="show($event.detail)"
        x-show="open"
        x-transition
        style="display: none !important"
        class="fixed inset-0 z-10 flex items-center justify-center text-left bg-black bg-opacity-50"
    >
        <form
            x-ref="form"
            action="{{ route('candidate-subscriptions.update', ['answer' => 'x']) }}"
            method="POST"
            class="inline-block bg-white shadow-xl rounded-lg py-10 px-4 text-gray-700 lg:w-1/3"
        >
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

                <template x-if="question.id == 'q51'">
                    <label for="value">
                        <span x-text="question.content" class="font-bold text-base"></span>
                        <select x-bind:name="question.id" class="form-select w-full mt-1">
                            <option value="sim">Sim</option>
                            <option value="não">Não</option>
                        </select>
                    </label>
                </template>

                <template x-if="question.id == 'q63'">
                    <label for="value">
                        <span x-text="question.content" class="font-bold text-base"></span>
                        <textarea x-bind:name="question.id" class="form-textarea w-full mt-1" rows="4">{{ $answers->for('q63')->value }}</textarea>
                    </label>
                </template>

            </div>

            <div class="flex justify-between mt-8">
                <button
                    @click.prevent="hide()"
                    type="button"
                    class="px-4 py-1 bg-red-500 text-white text-sm uppercase font-semibold rounded-md shadow-md hover:bg-red-700"
                >
                    Cancelar
                </button>
                <button
                    @click.prevent="submit()"
                    class="px-4 py-1 bg-blue-500 text-white text-sm uppercase font-semibold rounded-md shadow-md hover:bg-blue-700"
                >
                    Confirmar
                </button>
            </div>
        </form>
    </div>

    <!-- Actions -->
    <div class="flex justify-between">
        <button
            onclick="print()"
            type="button"
            class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-blue-600 hover:bg-blue-500 hover:text-blue-100 rounded-md shadown"
        >
            baixar PDF
        </button>
        <form
            x-data="destroy()"
            x-ref="form"
            @keydown.window.escape="hide()"
            action="{{ route('candidate-subscriptions.destroy', ['entry' => $entry]) }}"
            method="POST"
        >
            @csrf
            @method('DELETE')

            <button
                @click.prevent="show()"
                type="submit"
                class="px-4 py-2 text-sm font-medium leading-none text-white capitalize bg-red-600 hover:bg-red-500 hover:text-blue-100 rounded-md shadown"
            >
                deletar cadastro
            </button>

            <div
                x-show="open"
                x-transition
                style="display: none !important"
                class="fixed inset-0 z-10 flex items-center justify-center text-left bg-black bg-opacity-50"
            >
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
                        <button
                            @click.prevent="hide()"
                            type="button"
                            class="px-4 py-1 bg-red-500 text-white text-sm uppercase font-semibold rounded-md shadow-md hover:bg-red-700"
                        >
                            Cancelar
                        </button>
                        <button
                            @click.prevent="submit()"
                            class="px-4 py-1 bg-blue-500 text-white text-sm uppercase font-semibold rounded-md shadow-md hover:bg-blue-700"
                        >
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
                    <x-icons.logo-ceproesc class="w-10/12 h-auto"/>
                </div>

                <div class="flex justify-start items-center w-1/2 pr-12">
                    <div class="w-full space-y-2 text-center uppercase leading-none text-gray-700">
                        <h1 class="text-3xl font-bold tracking-wider">
                            {{ $answers->for('q1')->value }} 
                        </h1>
                        <p class="text-xl">
                            {{ \Carbon\Carbon::parse($answers->for('q3')->value)->diff(\Carbon\Carbon::now())->format('%y anos') }}
                        </p>
                        <p class="text-xl">
                            {{ \Carbon\Carbon::parse($answers->for('q3')->value)->format('d/m/Y') }}
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
                                    {{ $answers->for('q4')->value }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                cnh:
                                <span class="font-normal capitalize">
                                    {{ $answers->for('q17')->value }}
                                    @if ($answers->for('q17')->value == 'sim')
                                    ({{ $answers->for('q18')->value }})
                                    @endif
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                rg:
                                <span class="font-normal capitalize">
                                    {{ $answers->for('q33')->value }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                cpf:
                                <span class="font-normal capitalize">
                                    {{ $answers->for('q28')->value }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                carteira de trabalho:
                                <span class="font-normal capitalize">
                                    {{ $answers->for('q31')->value }}
                                    - Nº {{ $answers->for('q32')->value }}
                                </span>
                            </p>

                            @if ($answers->for('q4')->value == 'masculino' && isset($answers->for('q35')->value))

                                <p class="text-xs uppercase font-bold">
                                    alistamento militar:
                                    <span class="font-normal capitalize">
                                        {{ $answers->for('q35')->value }}
                                    </span>
                                </p>

                                @unless ($answers->for('q35')->value == 'ainda não convocado')
                                    <p class="text-xs uppercase font-bold">
                                        número de reservista:
                                        <span class="font-normal capitalize">
                                            {{ $answers->for('q36')->value }}
                                        </span>
                                    </p>
                                @endunless

                            @endif

                            <p class="text-xs uppercase font-bold">
                                título de eleitor:
                                <span class="font-normal capitalize">
                                    {{ $answers->for('q34')->value }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                escolaridade:
                                @if ($answers->for('q37')->value == 'ensino superior')
                                    <span class="font-normal capitalize">
                                        {{ $answers->for('q37')->value }} - 
                                        {{ optional($answers->for('q40'))->value }}
                                        ({{ $answers->for('q39')->value }}) - 
                                        {{ $answers->for('q38')->value }}

                                    </span>
                                @else
                                    <span class="font-normal capitalize">
                                        {{ $answers->for('q37')->value }} -
                                        {{ $answers->for('q38')->value }}
                                    </span>
                                @endif
                            </p>

                            <p class="text-xs uppercase font-bold">
                                conhecimentos em informática:
                                <span class="font-normal capitalize">
                                    {{ $answers->for('q41')->value }}
                                    @unless ($answers->for('q42')->value == 'nenhum')
                                    <span class="normal-case">
                                        ({{ $answers->allFor('q42')->pluck('value')->join(', ', ' e ') }})
                                    </span>
                                    @endunless
                                </span>
                            </p>

                            @if ($answers->for('q43')->value == 'sim')
                            <p class="text-xs uppercase font-bold">
                                cursos complementares:
                                <span class="font-normal normal-case">
                                    {{ $answers->allFor('q44')->pluck('value')->join(', ', ' e ') }}
                                    ({{ $answers->allFor('q45')->pluck('value')->join(', ', ' e ') }})
                                </span>
                            </p>
                            @endif

                            <p class="text-xs uppercase font-bold">
                                habilidade manual:
                                <span class="font-normal capitalize">
                                    {{ $answers->for('q5')->value }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                naturalidade:
                                <span class="font-normal capitalize">
                                    {{ $answers->for('q7')->value }}/{{ $answers->for('q6')->value }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                família recebe auxílio do governo:
                                <span class="font-normal capitalize">
                                    {{ $answers->for('q27')->value }}
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
                                    {{ $answers->for('q15')->value }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                recado:
                                <span class="font-normal capitalize">
                                    {{ $answers->for('q16')->value }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                e-mail:
                                <span class="font-normal normal-case">
                                    {{ $answers->for('q2')->value }}
                                </span>
                            </p>

                            <p class="text-xs uppercase font-bold">
                                endereço:
                                <span class="font-normal normal-case">
                                    {{ $answers->for('q9')->value }}, {{ $answers->for('q10')->value }}, {{ $answers->for('q13')->value }}, {{ $answers->for('q8')->value }} - CEP: {{ $answers->for('q11')->value }} - Zona {{ $answers->for('q12')->value }}
                                </span>
                            </p>

                        </div>

                    </article>

                    @if ($answers->for('q21')->value > 0 && $answers->for('q22') !== null)
                    <article>

                        <h2 class="text-lg font-bold text-blue-800 uppercase tracking-wider">moradores da residência</h2>

                        <div class="mt-1 space-y-4 text-xs">

                            @php
                            $residents['q22'] = $answers->allFor('q22');
                            $residents['q23'] = $answers->allFor('q23');
                            $residents['q24'] = $answers->allFor('q24');
                            $residents['q25'] = $answers->allFor('q25');
                            $residents['q26'] = $answers->allFor('q26');
                            @endphp
                            @for ($i = 0; $i < $answers->for('q21')->value; $i++)
                                <div>
                                    <p>
                                        {{ Str::words($residents['q22']->shift()->value, 1, ',') }}
                                        {{ $residents['q24']->shift()->value }}
                                        ({{ $residents['q23']->shift()->value }}):
                                        {{ $residents['q25']->shift()->value }}
                                    </p>
                                    <p>
                                        Renda: {{ $residents['q26']->shift()->value }}
                                    </p>
                                </div>
                            @endfor

                        </div>

                    </article>
                    @endif

                </div>

                <div class="w-1/2 space-y-6">

                    @if ($answers->for('q47')->value == 'sim')
                        <article>

                            <h2 class="text-lg font-bold text-blue-800 uppercase tracking-wider">histórico de trabalho</h2>

                            <div class="mt-1">
                                @foreach ($answers->allIn(['q48', 'q49', 'q50']) as $answer)

                                    <p class="text-xs uppercase font-bold">
                                        {{ $answer->question->content }}:
                                        <span class="font-normal normal-case">
                                            {{ ucfirst($answer->value) }}
                                        </span>
                                    </p>

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
                                {{ ucfirst($answers->for('q52')->value) }}
                            </p>

                            <p class="font-bold mt-2">
                                Com qual desses comportamentos você mais se identifica?
                            </p>
                            <p class="font-normal capitalize">
                                {{ ucfirst($answers->for('q54')->value) }}
                            </p>

                            <p class="font-bold mt-2">
                                Uma música para se ouvir todos os dias
                            </p>
                            <p class="font-normal">
                                {{ ucfirst($answers->for('q56')->value) }}
                            </p>

                            <p class="font-bold mt-2">
                                Quando você se olha no espelho, você enxerga
                            </p>
                            <p class="font-normal">
                                {{ ucfirst($answers->allFor('q58')->pluck('value')->join(', ', ' e ')) }}
                            </p>

                            <p class="font-bold mt-2">
                                Qual profissão gostaria de ter?
                            </p>
                            <p class="font-normal">
                                {{ ucfirst($answers->for('q60')->value) }}
                            </p>

                            <p class="font-bold mt-2">
                                Expectativas com o programa jovem aprendiz/estágio
                            </p>
                            <p class="font-normal">
                                {{ ucfirst($answers->for('q53')->value) }}
                            </p>

                            <p class="font-bold mt-2">
                                Uma frase que resume sua vida
                            </p>
                            <p class="font-normal">
                                {{ ucfirst($answers->for('q55')->value) }}
                            </p>

                            <p class="font-bold mt-2">
                                Para você pode faltar tudo, menos...
                            </p>
                            <p class="font-normal">
                                {{ ucfirst($answers->for('q57')->value) }}
                            </p>

                            <p class="font-bold mt-2">
                                O que você acrescentaria na sua personalidade?
                            </p>
                            <p class="font-normal">
                                {{ ucfirst($answers->for('q59')->value) }}
                            </p>

                            <p class="font-bold mt-2">
                                Deixe uma mensagem para a humanidade
                            </p>
                            <p class="font-normal">
                                {{ ucfirst($answers->for('q61')->value) }}
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
            
                    @foreach ($answers->allIn($sections[0]) as $answer)
            
                        @if ($answer->question->key == 'q3')
            
                            <x-card.list.description-item
                                :label="$answer->question->content"
                                :description="\Carbon\Carbon::parse($answer->value)->format('d/m/Y')"
                                :layout="true"
                            />
            
                            <x-card.list.description-item
                                label="idade"
                                :description="\Carbon\Carbon::parse($answer->value)->diff(now())->format('%y anos')"
                                :layout="true"
                            />
            
                        @continue
            
                        @endif
            
                        <x-card.list.description-item
                            :label="$answer->question->content"
                            :description="$answer->value"
                            :type="in_array($answer->question->key, ['q2', 'q19', 'q20']) ? 'text' : 'title'"
                            :layout="true"
                        />
            
                    @endforeach
            
                </x-slot>
            
            </x-card.list.description-layout>
            
            <x-card.list.description-layout title="dados familiares">
            
                <x-slot name="items">
            
                    @foreach ($answers->allIn($sections[1]) as $answer)
            
                        <x-card.list.description-item
                            :label="$answer->question->content"
                            :description="$answer->value"
                            :type="in_array($answer->question->key, ['q2', 'q19', 'q20']) ? 'text' : 'title'"
                            :layout="true"
                        />
            
                    @endforeach
            
                </x-slot>
            
            </x-card.list.description-layout>
            
            <x-card.list.description-layout title="documentação">
            
                <x-slot name="items">
            
                    @foreach ($answers->allIn($sections[2]) as $answer)
            
                        <x-card.list.description-item
                            :label="$answer->question->content"
                            :description="$answer->value"
                            :layout="true"
                        />
            
                    @endforeach
            
                </x-slot>
            
            </x-card.list.description-layout>
            
        </div>

        <div class="space-y-8">

            <x-card.list.description-layout title="escolaridade">
            
                <x-slot name="items">
            
                    @foreach ($answers->allIn($sections[3]) as $answer)
            
                        <x-card.list.description-item
                            :label="$answer->question->content"
                            :description="$answer->value"
                            :layout="true"
                        />
            
                    @endforeach
            
                </x-slot>
            
            </x-card.list.description-layout>

            <x-card.list.description-layout title="experiência">
            
                <x-slot name="items">
            
                    <x-card.list.description-item
                        :label="$answers->for('q41')->question->content"
                        :description="$answers->for('q41')->value"
                        :layout="true"
                    />
        
                    <x-card.list.description-item
                        :label="$answers->for('q42')->question->content"
                        :description="ucfirst($answers->allFor('q42')->pluck('value')->join(', ', ' e '))"
                        :layout="true"
                        type="text"
                    />
        
                    @foreach ($answers->allIn($sections[4]) as $answer)
            
                        @if (in_array($answer->question->key, ['q41', 'q42']))
            
                            @continue
            
                        @endif

                        @if ($answer->question->key === 'q51')
            
                            <div class="relative">
                                <x-card.list.description-item
                                    x-data="
                                        {{ json_encode(['data' => ['answer' => $answer->id, 'question' => [ 'id' => 'q51', 'content' => $answer->question->content]]]) }}
                                    "
                                    :label="$answer->question->content"
                                    :description="$answer->value"
                                    :layout="true"
                                >
                                    <button
                                        x-on:click.prevent="$dispatch('edit', data)"
                                        type="button"
                                        class="absolute right-0 text-gray-300 hover:text-blue-300"
                                    >
                                        <x-icons.edit class="w-6 h-6"/>
                                    </button>
                                </x-card.list.description-item>
                            </div>

                            @continue
                
                        @endif

                        <x-card.list.description-item
                            :label="$answer->question->content"
                            :description="$answer->value"
                            :layout="true"
                        />
            
                    @endforeach
            
                </x-slot>
            
            </x-card.list.description-layout>

            <x-card.list.description-layout title="sobre você">
            
                <x-slot name="items">
            
                    @foreach ($answers->allIn($sections[5]) as $answer)
            
                        @if ($answer->question->key == 'q58')
            
                            @break
            
                        @endif

                        @if ($answer->question->key == 'q53')

                            <x-card.list.description-item
                                :label="str_replace(':', '/Estágio:', $answer->question->content)"
                                :description="$answer->value"
                                :type="$answer->question->type == 'textarea' ? 'text' : 'title'"
                                :linebreak="$answer->question->type == 'textarea'"
                                :layout="true"
                            />
            
                            @continue
            
                        @endif

                        <x-card.list.description-item
                            :label="$answer->question->content"
                            :description="$answer->value"
                            :type="$answer->question->type == 'textarea' ? 'text' : 'title'"
                            :linebreak="$answer->question->type == 'textarea'"
                            :layout="true"
                        />
            
                    @endforeach
            
                    <x-card.list.description-item
                        :label="$answer->question->content"
                        :description="ucfirst($answers->allFor('q58')->pluck('value')->join(', ', ' e '))"
                        :layout="true"
                        type="text"
                    />

                    @foreach ($answers->allIn($sections[5]) as $answer)
            
                        @if (in_array($answer->question->key, ['q52', 'q53', 'q54', 'q55', 'q56', 'q57', 'q58']))
            
                        @continue
            
                        @endif

                        <x-card.list.description-item
                            :label="$answer->question->content"
                            :description="$answer->value"
                            :type="$answer->question->type == 'textarea' ? 'text' : 'title'"
                            :linebreak="$answer->question->type == 'textarea'"
                            :layout="true"
                        />
            
                    @endforeach
                </x-slot>
            
            </x-card.list.description-layout>

        </div>
    </div>

    <x-card.list.description-layout title="interno">
    
        <x-slot name="items">
    
            <x-card.list.description-item
                x-data="
                    {{ json_encode(['data' => ['answer' => $answers->for('q63')->id, 'question' => [ 'id' => 'q63', 'content' => $answers->for('q63')->question->content]]]) }}
                "
                :label="$answers->for('q63')->question->content"
                :description="$answers->for('q63')->value"
                type="text"
                :linebreak="true"
            >
                <button
                    x-on:click.prevent="$dispatch('edit', data)"
                    type="button"
                    class="text-gray-300 hover:text-blue-300"
                >
                    <x-icons.edit class="w-6 h-6"/>
                </button>
            </x-card.list.description-item>
    
        </x-slot>
    
    </x-card.list.description-layout>

</section>


@endsection

@push('footer')
<script>
    function print() {
        filename =  'ficha-{{ Str::slug($answers->for('q1')->value, '-') }}.pdf';
        opt = {
            filename: filename,
            image: { type: 'jpeg', quality: 1 },
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

            answer: null,

            question: {
                id: null,
                content: null
            },

            show(data) {
                this.answer = data.answer
                this.question = data.question

                this.open = true
                document.body.classList.add('overflow-hidden')
            },
            hide() {
                this.open = false
                document.body.classList.remove('overflow-hidden')
            },

            submit() {
                action = this.$refs.form.action.replace('/x', `/${this.answer}`)
                this.$refs.form.action = action

                this.$refs.form.submit()
            }
        }
    }
</script>
@endpush
