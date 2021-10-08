<x-guest-layout>

    <x-slot name="head">
        <script defer src="https://unpkg.com/alpinejs@3.4.2/dist/cdn.min.js"></script>
    </x-slot>

    <section x-data="modal()" @keydown.window.escape="hide()" class="container mx-auto px-2 py-8">

        <x-icons.logo-ceproesc class="mx-auto w-64 h-auto -mt-8"/>
        <a href="https://ceproesc.com.br/" class="block -mt-4 text-center text-sm underline text-gray-500 hover:text-green-500">Voltar para a home</a>

        <h1 class="mt-8 text-center font-semibold text-3xl lg:text-4xl">Formulário do Candidato</h1>
        <span class="block italic text-sm text-center text-gray-500">*Todos os campos são <strong>obrigatórios</strong>.</span>
        <span class="block mt-2 font-semibold text-center text-gray-400 text-xs">Atenção: se você já tem um cadastro, esta nova ficha irá substituir o seu cadastro anterior.</span>

        @if ($errors->any())
            <ul class="mt-4 py-4 bg-red-300 text-center text-red-700 font-semibold rounded-md lg:container lg:mx-auto lg:max-w-screen-lg">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form x-ref="form" action="{{ route('cadidate-subscriptions.store') }}" method="POST" class="space-y-4 lg:container lg:mx-auto lg:max-w-screen-lg lg:space-y-8">
            @csrf

            <section class="px-2 py-4">
            
                <h2 class="text-center text-xl lg:text-2xl">Dados Cadastrais</h2>

                <div class="mt-4 space-y-4 lg:grid lg:grid-cols-3 lg:gap-6 lg:space-y-0">

                    @foreach ($form->sections[0]->questions as $question)

                        @if ($question->key === 'q4')

                            <x-dynamic-component
                                x-data=""
                                x-on:change="$dispatch('q4', $event.target.value)"
                                :component="'candidate-subscription.' . $question->type"
                                :name="$question->key"
                                :label="$question->content"
                                :options="$question->options"
                                :value="old($question->key)"
                            />

                            @continue

                        @endif

                        <x-dynamic-component
                            :component="'candidate-subscription.' . $question->type"
                            :name="$question->key"
                            :label="$question->content"
                            :options="$question->options"
                            :value="old($question->key)"
                        />

                        @break($loop->iteration == 10)

                    @endforeach

                    <x-dynamic-component
                        x-data="{input: '{{ old($form->sections[0]->questions[10]->key, '') }}'}"
                        x-init="
                            $watch('input', (value, oldvalue) => {
                                if (oldvalue < value) {
                                    input = value.replace(/[^0-9]/g, '').replace(/^(\d{2})?(\d{3})?(\d{3})?/g, '$1.$2-$3').substr(0, 10)
                                }
                            });
                            $el.lastElementChild.setAttribute('x-model', 'input');
                        "
                        :component="'candidate-subscription.' . $form->sections[0]->questions[10]->type"
                        :name="$form->sections[0]->questions[10]->key"
                        :label="$form->sections[0]->questions[10]->content"
                        :options="$form->sections[0]->questions[10]->options"
                    />

                    @foreach ($form->sections[0]->questions as $question)

                    @continue($loop->iteration < 12)

                    <x-dynamic-component
                        :component="'candidate-subscription.' . $question->type"
                        :name="$question->key"
                        :label="$question->content"
                        :options="$question->options"
                        :value="old($question->key)"
                    />

                    @break($loop->iteration == 14)

                    @endforeach

                    <x-dynamic-component
                        x-data="{input: '{{ old($form->sections[0]->questions[14]->key, '') }}'}"
                        x-init="
                            $watch('input', (value, oldvalue) => {
                                if (oldvalue < value) {
                                    input = value.replace(/[^0-9]/g, '').replace(/^(\d{2})?(\d{5})?(\d{4})?/g, '($1) $2-$3').substr(0, 15)
                                }
                            });
                            $el.lastElementChild.setAttribute('x-model', 'input');
                        "
                        :component="'candidate-subscription.' . $form->sections[0]->questions[14]->type"
                        :name="$form->sections[0]->questions[14]->key"
                        :label="$form->sections[0]->questions[14]->content"
                        :options="$form->sections[0]->questions[14]->options"
                    />

                    <x-dynamic-component
                        x-data="{input: '{{ old($form->sections[0]->questions[15]->key, '') }}'}"
                        x-init="
                            $watch('input', (value, oldvalue) => {
                                if (oldvalue < value) {
                                    input = value.replace(/[^0-9]/g, '').replace(/^(\d{2})?(\d{5})?(\d{4})?/g, '($1) $2-$3').substr(0, 15)
                                }
                            });
                            $el.lastElementChild.setAttribute('x-model', 'input');
                        "
                        :component="'candidate-subscription.' . $form->sections[0]->questions[15]->type"
                        :name="$form->sections[0]->questions[15]->key"
                        :label="$form->sections[0]->questions[15]->content"
                        :options="$form->sections[0]->questions[15]->options"
                    />

                    @foreach ($form->sections[0]->questions as $question)

                        @continue($loop->iteration < 17)

                        @if ($question->key == 'q18')
                            <x-dynamic-component
                                :component="'candidate-subscription.' . $question->type"
                                :name="$question->key"
                                :label="$question->content"
                                :options="$question->options"
                                :value="old($question->key)"
                                legend="*Se não tem CNH, escolha 'Nenhum'"
                            />

                            @continue
                        @endif

                        <x-dynamic-component
                            :component="'candidate-subscription.' . $question->type"
                            :name="$question->key"
                            :label="$question->content"
                            :options="$question->options"
                            :value="old($question->key)"
                        />

                    @endforeach

                </div>

            </section>

            <section class="px-2 py-4">
            
                <h2 class="text-center text-xl lg:text-2xl">Dados Familiares</h2>

                <div class="mt-4 space-y-4">

                    @php
                        $questions = $form->sections[1]->questions;
                    @endphp

                    <x-dynamic-component
                        x-data=""
                        x-on:change="$dispatch('q21', $event.target.value)"
                        :component="'candidate-subscription.' . $questions[0]->type"
                        :name="$questions[0]->key"
                        :label="$questions[0]->content"
                        :value="old($questions[0]->key)"
                        min="0"
                        max="10"
                    />

                    <div
                        class="px-4 py-2 space-y-8 lg:grid lg:grid-cols-3 lg:gap-6 lg:space-y-0"
                        x-data="{
                            quantity: Array.from({ length: {{ old($questions[0]->key, 0) }} }),
                            update: function(value) {
                                if (value > 10) {
                                    value = 10;
                                } else if (value < 0) {
                                    value = 0;
                                }

                                this.quantity = Array.from({ length: value });
                            },
                            money: [], 
                            old: {{ json_encode(Arr::flatten(old('q21-group', []))) }},
                            setOldInput() {
                                if (this.old.length == 0) {
                                    return ''
                                }
                                old = this.old[0]
                                this.old.shift()
                                return old
                            }
                        }"
                        @q21.window="update($event.detail)"
                    >
                        <template x-for="i in quantity">
                            <div>

                                @for ($i = 1; $i < 5; $i++)
                                <x-dynamic-component
                                    :component="'candidate-subscription.' . $questions[$i]->type"
                                    :name="'q21-group[][' . $questions[$i]->key . ']'"
                                    :label="$questions[$i]->content"
                                    x-init="$el.lastElementChild.value = setOldInput()"
                                />
                                @endfor

                                <x-dynamic-component
                                    x-init="money[i] = 0;$el.lastElementChild.value = setOldInput()"
                                    x-on:input="
                                        value = $event.target.value;
                                        if (value.length > money[i]) {
                                            money[i] = value.length;
                                            value = value.replace(',00', '');
                                            value = value.replace(/[^0-9]/g, '').substr(0, 6);
                                            p1 = value.slice(0,-3);
                                            p2 = value.slice(-3);
                                            value = value.length > 3 ? p1 + '.' + p2 : value;
                                            $el.lastElementChild.value = 'R$ '+ value + ',00';
                                        } else {
                                            money[i] = value.length;
                                            $el.lastElementChild.value = value;
                                        }
                                    "
                                    :component="'candidate-subscription.' . $questions[5]->type"
                                    :name="'q21-group[][' . $questions[5]->key . ']'"
                                    :label="$questions[5]->content"
                                    :value="old($questions[5]->key)"
                                />

                            </div>
                        </template>
                    </div>

                    <x-dynamic-component
                        :component="'candidate-subscription.' . $questions[6]->type"
                        :name="$questions[6]->key"
                        :label="$questions[6]->content"
                        :options="$questions[6]->options"
                        :value="old($questions[6]->key)"
                    />

                </div>

            </section>

            <section class="px-2 py-4">
            
                <h2 class="text-center text-xl lg:text-2xl">Documentação</h2>

                <div class="mt-4 space-y-4 lg:grid lg:grid-cols-3 lg:gap-6 lg:space-y-0">

                    @php
                        $questions = $form->sections[2]->questions;
                    @endphp

                    <x-dynamic-component
                        x-data="{input: '{{ old($questions[0]->key, '') }}'}"
                        x-init="
                            $watch('input', (value, oldvalue) => {
                                if (oldvalue < value) {
                                    input = value.replace(/[^0-9]/g, '').replace(/^(\d{3})?(\d{3})?(\d{3})?(\d{2})?/g, '$1.$2.$3-$4').substr(0, 14)
                                }
                            });
                            $el.lastElementChild.setAttribute('x-model', 'input');
                        "
                        :component="'candidate-subscription.' . $questions[0]->type"
                        :name="$questions[0]->key"
                        :label="$questions[0]->content"
                        :options="$questions[0]->options"
                    />

                    <x-dynamic-component
                        x-data="{input: '{{ old($questions[1]->key, '') }}'}"
                        x-init="
                            $watch('input', (value, oldvalue) => { if (oldvalue < value) { input = value.replace(/[^0-9]/g, '').replace(/^(\d{3})?(\d{3})?(\d{3})?(\d{2})?/g, '$1.$2.$3-$4').substr(0, 14) }});
                            $el.lastElementChild.setAttribute('x-model', 'input');
                        "
                        :component="'candidate-subscription.' . $questions[1]->type"
                        :name="$questions[1]->key"
                        :label="$questions[1]->content"
                        :options="$questions[1]->options"
                        legend="Se maior de idade, digite seu próprio CPF."
                    />

                    @foreach ($form->sections[2]->questions as $question)

                        @continue($loop->iteration < 3)

                        @if (in_array($question->key, ['q35', 'q36']))
                            
                            <div
                                x-data="{show: false}"
                                x-on:q4.window="show = $event.detail == 'masculino' ? true : false"
                            >
                                <template x-if="show">
                                    <x-dynamic-component
                                        :component="'candidate-subscription.' . $question->type"
                                        :name="$question->key"
                                        :label="$question->content"
                                        :options="$question->options"
                                        :value="old($question->key)"
                                    />
                                </template>
                            </div>

                            @continue

                        @elseif ($question->key == 'q30')
                            <x-dynamic-component
                                :component="'candidate-subscription.' . $question->type"
                                :name="$question->key"
                                :label="$question->content"
                                :options="$question->options"
                                :value="old($question->key)"
                                legend="Se maior de idade, digite seu próprio nome."
                            />

                            @continue

                        @elseif ($question->key == 'q32')
                            <x-dynamic-component
                                :component="'candidate-subscription.' . $question->type"
                                :name="$question->key"
                                :label="$question->content"
                                :options="$question->options"
                                :value="old($question->key)"
                                legend="Se carteira de trabalho digital, o número é seu CPF."
                            />

                            @continue

                        @endif

                        <x-dynamic-component
                            :component="'candidate-subscription.' . $question->type"
                            :name="$question->key"
                            :label="$question->content"
                            :options="$question->options"
                            :value="old($question->key)"
                        />

                    @endforeach

                </div>

            </section>

            <section class="px-2 py-4">
            
                <h2 class="text-center text-xl lg:text-2xl">Escolaridade</h2>

                <div class="mt-4 space-y-4 lg:grid lg:grid-cols-4 lg:gap-6 lg:space-y-0">

                    @php
                        $questions = $form->sections[3]->questions;
                    @endphp

                    <x-dynamic-component
                        x-data=""
                        x-on:change="$dispatch('q38', $event.target.value)"
                        :component="'candidate-subscription.' . $questions[0]->type"
                        :name="$questions[0]->key"
                        :label="$questions[0]->content"
                        :options="$questions[0]->options"
                        :value="old($questions[0]->key)"
                    />

                    <x-dynamic-component
                        :component="'candidate-subscription.' . $questions[1]->type"
                        :name="$questions[1]->key"
                        :label="$questions[1]->content"
                        :options="$questions[1]->options"
                        :value="old($questions[1]->key)"
                    />

                    <x-dynamic-component
                        :component="'candidate-subscription.' . $questions[2]->type"
                        :name="$questions[2]->key"
                        :label="$questions[2]->content"
                        :options="$questions[2]->options"
                        :value="old($questions[2]->key)"
                    />

                    <div
                        x-data="{show: false}"
                        x-on:q38.window="show = $event.detail == 'ensino superior' ? true : false"
                    >
                        <template x-if="show">
                            <x-dynamic-component
                                :component="'candidate-subscription.' . $questions[3]->type"
                                :name="$questions[3]->key"
                                :label="$questions[3]->content"
                                :options="$questions[3]->options"
                                :value="old($questions[3]->key)"
                                legend="Digite o curso e o semestre que você está cursando."
                            />
                        </template>
                    </div>

                </div>

            </section>

            <section class="px-2 py-4">
            
                <h2 class="text-center text-xl lg:text-2xl">Experiência</h2>

                <div class="mt-4 space-y-4 lg:grid lg:grid-cols-2 lg:gap-6 lg:space-y-0">

                    @php
                        $questions = $form->sections[4]->questions;
                    @endphp

                    <x-dynamic-component
                        :component="'candidate-subscription.' . $questions[0]->type"
                        :name="$questions[0]->key"
                        :label="$questions[0]->content"
                        :options="$questions[0]->options"
                        :value="old($questions[0]->key)"
                    />

                    <x-dynamic-component
                        :component="'candidate-subscription.' . $questions[1]->type"
                        name="q42-group[][q42]"
                        :label="$questions[1]->content"
                        :options="$questions[1]->options"
                        :value="Arr::flatten(old('q42-group', []))"
                    />

                    <x-dynamic-component
                        x-data=""
                        x-on:change="$dispatch('q43', $event.target.value)"
                        :component="'candidate-subscription.' . $questions[2]->type"
                        :name="$questions[2]->key"
                        :label="$questions[2]->content"
                        :options="$questions[2]->options"
                        :value="old($questions[2]->key)"
                        class="lg:col-span-2"
                    />

                    <div
                        x-data="{
                            show: {{ old('q43', 'false') == 'sim' ? 'true' : 'false' }},
                            quantity: Array.from({ length: {{ count(Arr::flatten(old('q43-group', []))) > 0 ? count(Arr::flatten(old('q43-group', [])))/3 : 1 }} }),
                            old: {{ json_encode(Arr::flatten(old('q43-group', []))) }},
                            setOldInput() {
                                if (this.old.length == 0) {
                                    return ''
                                }
                                old = this.old[0]
                                this.old.shift()
                                return old
                            }
                        }"
                        @q43.window="show = $event.detail == 'sim' ? true : false"
                        class="lg:col-span-2"
                    >
                        <template x-if="show">
                            <div>
                                <div class="px-4 py-2 space-y-8 lg:grid lg:grid-cols-3 lg:gap-6 lg:space-y-0">
                                    <template x-for="i in quantity">
                                        <div class="lg:space-y-2">
                                            @for ($i = 3; $i < 6; $i++)
                                            <x-dynamic-component
                                                :component="'candidate-subscription.' . $questions[$i]->type"
                                                :name="'q43-group[][' . $questions[$i]->key . ']'"
                                                :label="$questions[$i]->content"
                                                x-init="$el.lastElementChild.value = setOldInput()"
                                            />
                                            @endfor
                                        </div>
                                    </template>
                                </div>
                                <div class="flex flex-col items-center mt-4 space-y-4">
                                    <button
                                        @click="quantity.push(quantity.length + 1)"
                                        x-bind:disabled="quantity.length == 10"
                                        type="button"
                                        class="mx-auto px-4 py-1 bg-blue-500 text-white text-sm uppercase font-semibold rounded-md shadow-md hover:bg-blue-700"
                                    >
                                        + adicionar capacitações
                                    </button>
                                    <button
                                        @click="quantity.pop()"
                                        x-bind:disabled="quantity.length == 1"
                                        type="button"
                                        class="mx-auto px-4 py-1 bg-red-500 text-white text-sm uppercase font-semibold rounded-md shadow-md hover:bg-red-700"
                                    >
                                        - remover capacitações
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <x-dynamic-component
                        x-data=""
                        x-on:change="$dispatch('q47', $event.target.value)"
                        :component="'candidate-subscription.' . $questions[6]->type"
                        :name="$questions[6]->key"
                        :label="$questions[6]->content"
                        :options="$questions[6]->options"
                        :value="old($questions[6]->key)"
                        class="lg:col-span-2"
                    />

                    <div
                        x-data="{
                            show: {{ old('q47', 'false') == 'sim' ? 'true' : 'false' }},
                            quantity: Array.from({ length: {{ count(Arr::flatten(old('q47-group', []))) > 0 ? count(Arr::flatten(old('q47-group', [])))/3 : 1 }} }),
                            old: {{ json_encode(Arr::flatten(old('q47-group', []))) }},
                            setOldInput() {
                                if (this.old.length == 0) {
                                    return ''
                                }
                                old = this.old[0]
                                this.old.shift()
                                return old
                            }
                        }"
                        @q47.window="show = $event.detail == 'sim' ? true : false"
                        class="lg:col-span-2"
                    >
                        <template x-if="show">
                            <div>
                                <div class="px-4 py-2 space-y-8 lg:grid lg:grid-cols-3 lg:gap-6 lg:space-y-0">
                                    <template x-for="i in quantity">
                                        <div class="lg:space-y-2">
                                            @for ($i = 7; $i < 10; $i++)
                                            <x-dynamic-component
                                                :component="'candidate-subscription.' . $questions[$i]->type"
                                                :name="'q47-group[][' . $questions[$i]->key . ']'"
                                                :label="$questions[$i]->content"
                                                x-init="$el.lastElementChild.value = setOldInput()"
                                            />
                                            @endfor
                                        </div>
                                    </template>
                                </div>
                                <div class="flex flex-col items-center mt-4 space-y-4">
                                    <button
                                        @click="quantity.push(quantity.length + 1)"
                                        x-bind:disabled="quantity.length == 10"
                                        type="button"
                                        class="mx-auto px-4 py-1 bg-blue-500 text-white text-sm uppercase font-semibold rounded-md shadow-md hover:bg-blue-700"
                                    >
                                        + adicionar experiências
                                    </button>
                                    <button
                                        @click="quantity.pop()"
                                        x-bind:disabled="quantity.length == 1"
                                        type="button"
                                        class="mx-auto px-4 py-1 bg-red-500 text-white text-sm uppercase font-semibold rounded-md shadow-md hover:bg-red-700"
                                    >
                                        - remover experiências
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <x-dynamic-component
                        :component="'candidate-subscription.' . $questions[10]->type"
                        :name="$questions[10]->key"
                        :label="$questions[10]->content"
                        :options="$questions[10]->options"
                        :value="old($questions[10]->key)"
                        class="lg:col-span-2"
                    />

                </div>

            </section>

            <section class="px-2 py-4">
            
                <h2 class="text-center text-xl lg:text-2xl">Sobre Você</h2>

                <div class="mt-4 space-y-4 lg:grid lg:grid-cols-2 lg:gap-6 lg:space-y-0">

                    @foreach ($form->sections[5]->questions as $question)
                    <x-dynamic-component
                        :component="'candidate-subscription.' . $question->type"
                        :name="$question->key"
                        :label="$question->content"
                        :options="$question->options"
                        :value="old($question->key)"
                    />

                    @break($loop->iteration == 6)

                    @endforeach

                    <x-dynamic-component
                        :component="'candidate-subscription.' . $form->sections[5]->questions[6]->type"
                        name="q58-group[][q58]"
                        :label="$form->sections[5]->questions[6]->content"
                        :options="$form->sections[5]->questions[6]->options"
                        :value="Arr::flatten(old('q58-group', []))"
                    />

                    @foreach ($form->sections[5]->questions as $question)

                    @continue($loop->iteration < 8)

                    <x-dynamic-component
                        :component="'candidate-subscription.' . $question->type"
                        :name="$question->key"
                        :label="$question->content"
                        :options="$question->options"
                        :value="old($question->key)"
                    />

                    @endforeach


                </div>

            </section>

            <div>
                <span class="block font-semibold text-center text-gray-400 text-xs">Atenção: se você já tem um cadastro, esta nova ficha irá substituir o seu cadastro anterior.</span>
                <button
                    @click.prevent="show()"
                    type="submit"
                    class="block mt-4 mx-auto px-4 py-1 bg-blue-500 text-white uppercase font-semibold rounded-md shadow-md hover:bg-blue-700"
                >
                    enviar inscrição
                </button>
            </div>

        </form>
        <div x-show="open" style="display: none !important" x-transition class="fixed inset-0 flex items-center justify-center text-left bg-black bg-opacity-50">
            <div class="inline-block bg-white shadow-xl rounded-lg py-10 px-4 text-gray-700 lg:w-1/3">
                <div>
                    <span class="block font-semibold text-center text-gray-400 text-xs">Atenção: se você já tem um cadastro, esta nova ficha irá substituir o seu cadastro anterior.</span>
                    <h3 class="mt-8 text-center font-semibold text-lg">Leia atentamente a política de privacidade:</h3>
                    <div class="max-h-80 overflow-y-scroll mt-2 py-4 px-2 space-y-4 bg-gray-100 rounded-lg">

                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                        <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                        <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
                        <p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem</p>
                        <p>Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>

                        <div>
                            <h3 class="font-bold text-base">Termos política de privacidade</h3>

                            <label class="inline-flex items-center">
                                <input x-model="accepted" required type="checkbox" class="form-checkbox">
                                <span class="ml-2" >Li e declaro que aceitos os termos da política de privacidade.</span>
                            </label>
                        </div>

                    </div>

                </div>
                <div class="flex justify-between mt-8">
                    <button @click="hide()" class="px-4 py-1 bg-red-500 text-white text-sm uppercase font-semibold rounded-md shadow-md hover:bg-red-700">Cancelar</button>
                    <button @click="submit()" x-bind:disabled="!accepted" :class="accepted || 'cursor-default opacity-50'" class="px-4 py-1 bg-blue-500 text-white text-sm uppercase font-semibold rounded-md shadow-md hover:bg-blue-700">Confirmar</button>
                </div>
            </div>
        </div>
    </section>

    <script>
        function modal() {
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
                accepted: false,
                submit() {
                    inputs = this.$refs.form.querySelectorAll("[required]")
                    for (el of inputs) {
                        if (!el.reportValidity()) {
                            this.hide()
                            return
                        }
                    }
                    this.$refs.form.submit()
                }
            }
        }
    </script>

</x-guest-layout>
