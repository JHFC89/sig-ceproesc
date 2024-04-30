<x-guest-layout>

    <x-slot name="head">
        <script defer src="https://unpkg.com/alpinejs@3.4.2/dist/cdn.min.js"></script>
    </x-slot>

    <section x-data="modal()" @keydown.window.escape="hide()" class="container mx-auto px-2 py-8">

        <x-icons.logo-ceproesc class="mx-auto w-64 h-auto"/>
        <a href="https://ceproesc.com.br/" class="block mt-4 text-center text-sm underline text-gray-500 hover:text-green-500">Voltar para a home</a>

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

        <form x-ref="form" action="{{ route('cadidates.store') }}" method="POST" class="space-y-4 lg:container lg:mx-auto lg:max-w-screen-lg lg:space-y-8">
            @csrf

            <section class="px-2 py-4">

                <h2 class="text-center text-xl lg:text-2xl">Dados Cadastrais</h2>

                <div class="mt-4 space-y-4 lg:grid lg:grid-cols-3 lg:gap-6 lg:space-y-0">
                    <x-candidate-subscription.text
                        name="nome"
                        label="Nome"
                        :value="old('nome')"
                        legend="Se usar nome social, coloque entre parênteses."
                    />
                    <x-candidate-subscription.text
                        name="email"
                        label="E-mail"
                        :value="old('email')"
                    />
                    <x-candidate-subscription.date
                        name="data_de_nascimento"
                        label="Data de nascimento"
                        :value="old('data_de_nascimento')"
                    />

                    <x-candidate-subscription.radio
                        name="genero"
                        label="Gênero"
                        :options="App\Models\AprendizForm::GENEROS"
                        x-data=""
                        x-on:change="$dispatch('genero', $event.target.value)"
                        :value="old('genero')"
                    />
                    <x-candidate-subscription.radio
                        name="habilidade_manual"
                        label="Habilidade manual"
                        :options="App\Models\AprendizForm::HABILIDADES_MANUAIS"
                        :value="old('habilidade_manual')"
                    />
                    <x-candidate-subscription.select
                        name="estado_de_naturalidade"
                        label="Estado de naturalidade"
                        :options="App\Models\AprendizForm::ESTADOS"
                        :value="old('estado_de_naturalidade')"
                    />

                    <x-candidate-subscription.text
                        name="cidade_onde_nasceu"
                        label="Cidade onde nasceu"
                        :value="old('cidade_onde_nasceu')"
                    />
                    <x-candidate-subscription.text
                        name="cidade_onde_mora"
                        label="Cidade onde mora"
                        :value="old('cidade_onde_mora')"
                    />
                    <x-candidate-subscription.text
                        name="logradouro"
                        label="Logradouro"
                        :value="old('logradouro')"
                    />

                    <x-candidate-subscription.text
                        name="numero"
                        label="Número"
                        :value="old('numero')"
                    />
                    <x-candidate-subscription.text
                        name="cep"
                        label="CEP"
                        :value="old('cep')"
                    />
                    <x-candidate-subscription.radio
                        name="zona"
                        label="Zona"
                        :options="App\Models\AprendizForm::ZONAS"
                        :value="old('zona')"
                    />

                    <x-candidate-subscription.text
                        name="bairro"
                        label="Bairro"
                        :value="old('bairro')"
                    />
                    <x-candidate-subscription.text
                        name="complemento"
                        label="Complemento"
                        :value="old('complemento')"
                    />
                    <x-candidate-subscription.text
                        name="telefone"
                        label="Telefone do candidato"
                        :value="old('telefone')"
                    />

                    <x-candidate-subscription.text
                        name="telefone_de_recado"
                        label="Telefone de recado"
                        :value="old('telefone_de_recado')"
                    />
                    <x-candidate-subscription.radio
                        name="carteira_de_habilitacao"
                        label="Carteira de habilitação"
                        :options="['sim', 'não']"
                        :value="old('carteira_de_habilitacao')"
                    />
                    <x-candidate-subscription.radio
                        name="categoria"
                        label="Categoria"
                        :options="App\Models\AprendizForm::CATEGORIAS_CNH"
                        legend='*Se não tem CNH, escolha "Nenhum".'
                        :value="old('categoria')"
                    />

                    <x-candidate-subscription.text
                        name="facebook"
                        label="Facebook"
                        :value="old('facebook')"
                    />
                    <x-candidate-subscription.text
                        name="instagram"
                        label="Instagram"
                        :value="old('instagram')"
                    />

                </div>

            </section>

            <section class="px-2 py-4">

                <h2 class="text-center text-xl lg:text-2xl">Dados Familiares</h2>

                <div class="mt-4 space-y-4">

                    <x-candidate-subscription.number
                        name="quantas_pessoas_moram_com_voce"
                        label="Quantas pessoas moram com você?"
                        min="0"
                        max="10"
                        x-data=""
                        x-on:change="$dispatch('moradores', $event.target.value)"
                        :value="old('quantas_pessoas_moram_com_voce')"
                    />

                    <div
                        class="px-4 py-2 space-y-8 lg:grid lg:grid-cols-3 lg:gap-6 lg:space-y-0"
                        x-data="{
                            quantity: Array.from({ length: {{ old('quantas_pessoas_moram_com_voce', 0) }} }),
                            update: function(value) {
                                if (value > 10) {
                                    value = 10;
                                } else if (value < 0) {
                                    value = 0;
                                }

                                this.quantity = Array.from({ length: value });
                            },
                            money: [],
                            old: {{ App\Models\AprendizForm::parseInputToJson(old('moradores', 'nada'), 5) }},
                            setOldInput(field, index) {
                                return this.old[index][field] || ''
                            },
                        }"
                        @moradores.window="update($event.detail)"
                    >
                        <template x-for="(value, index) in quantity">
                            <div>

                                <x-candidate-subscription.text
                                    name="moradores[][nome_do_morador]"
                                    label="Nome do morador"
                                    x-init="$el.lastElementChild.value = setOldInput('Nome do morador', index)"
                                />
                                <x-candidate-subscription.text
                                    name="moradores[][parentesco]"
                                    label="Parentesco"
                                    x-init="$el.lastElementChild.value = setOldInput('Parentesco', index)"
                                />
                                <x-candidate-subscription.text
                                    name="moradores[][idade_do_morador]"
                                    label="Idade do morador"
                                    x-init="$el.lastElementChild.value = setOldInput('Idade do morador', index)"
                                />
                                <x-candidate-subscription.text
                                    name="moradores[][ocupacao]"
                                    label="Ocupação"
                                    x-init="$el.lastElementChild.value = setOldInput('Ocupação', index)"
                                />
                                <x-candidate-subscription.text
                                    name="moradores[][renda]"
                                    label="Renda"
                                    x-init="money[index] = 0;$el.lastElementChild.value = setOldInput('Renda', index)"
                                    x-on:input="
                                        value = $event.target.value;
                                        if (value.length > money[index]) {
                                            money[index] = value.length;
                                            value = value.replace(',00', '');
                                            value = value.replace(/[^0-9]/g, '').substr(0, 6);
                                            p1 = value.slice(0,-3);
                                            p2 = value.slice(-3);
                                            value = value.length > 3 ? p1 + '.' + p2 : value;
                                            $el.lastElementChild.value = 'R$ '+ value + ',00';
                                        } else {
                                            money[index] = value.length;
                                            $el.lastElementChild.value = value;
                                        }
                                    "
                                />

                            </div>
                        </template>
                    </div>

                    <x-candidate-subscription.radio
                        name="a_familia_recebe_algum_auxilio_do_governo"
                        label="A família recebe algum auxílio do governo?"
                        :options="['sim', 'não']"
                        :value="old('a_familia_recebe_algum_auxilio_do_governo')"
                    />

                </div>

            </section>

            <section class="px-2 py-4">

                <h2 class="text-center text-xl lg:text-2xl">Documentação</h2>

                <div class="mt-4 space-y-4 lg:grid lg:grid-cols-3 lg:gap-6 lg:space-y-0">

                    <x-candidate-subscription.text
                        name="cpf"
                        label="CPF"
                        :value="old('cpf')"
                    />
                    <x-candidate-subscription.text
                        name="cpf_do_responsavel"
                        label="CPF do responsável"
                        legend="Se maior de idade, digite seu próprio CPF."
                        :value="old('cpf_do_responsavel')"
                    />
                    <x-candidate-subscription.text
                        name="nome_do_responsavel"
                        label="Nome do responsável"
                        legend="Se maior de idade, digite seu próprio nome"
                        :value="old('nome_do_responsavel')"
                    />

                    <x-candidate-subscription.radio
                        name="carteira_de_trabalho"
                        label="Carteira de trabalho"
                        :options="App\Models\AprendizForm::CARTEIRA_DE_TRABALHO"
                        :value="old('carteira_de_trabalho')"
                    />
                    <x-candidate-subscription.text
                        name="numero_de_serie"
                        label="Número de série"
                        legend="Se carteira de trabalho digital, o número é seu CPF."
                        :value="old('numero_de_serie')"
                    />
                    <x-candidate-subscription.text
                        name="rg"
                        label="RG/UF"
                        :value="old('rg')"
                    />

                    <x-candidate-subscription.text
                        name="titulo_de_eleitor"
                        label="Título de eleitor"
                        legend='Se você não tiver título de eleitor, preencha com um "x"'
                        :value="old('titulo_de_eleitor')"
                    />
                    <div
                        x-data="{show: false, old: '{{ old('genero', 'no-value') }}'}"
                        x-on:genero.window="show = $event.detail == 'masculino'"
                    >
                        <template x-if="show || old == 'masculino'">
                            <div>
                                <x-candidate-subscription.radio
                                    name="alistamento_militar"
                                    label="Alistamento militar"
                                    :options="App\Models\AprendizForm::ALISTAMENTO_MILITAR"
                                    :value="old('alistamento_militar')"
                                />
                            </div>
                        </template>
                    </div>
                    <div
                        x-data="{show: false, old: '{{ old('genero', 'no-value') }}'}"
                        x-on:genero.window="show = $event.detail == 'masculino'"
                    >
                        <template x-if="show || old == 'masculino'">
                            <div>
                                <x-candidate-subscription.text
                                    name="numero_de_reservista"
                                    label="Número de reservista"
                                    legend='Se você não tiver número de reservista, preencha com um "x".'
                                    :value="old('numero_de_reservista')"
                                />
                            </div>
                        </template>
                    </div>
                </div>

            </section>

            <section class="px-2 py-4">

                <h2 class="text-center text-xl lg:text-2xl">Escolaridade</h2>

                <div class="mt-4 space-y-4 lg:grid lg:grid-cols-4 lg:gap-6 lg:space-y-0">

                    <x-candidate-subscription.radio
                        name="escolaridade"
                        label="Escolaridade"
                        :options="App\Models\AprendizForm::ESCOLARIDADE"
                        x-data=""
                        x-on:change="$dispatch('escolaridade', $event.target.value)"
                        :value="old('escolaridade')"
                    />
                    <x-candidate-subscription.radio
                        name="situacao_escolaridade"
                        label="Situação"
                        :options="App\Models\AprendizForm::SITUACAO_ESCOLARIDADE"
                        :value="old('situacao_escolaridade')"
                    />
                    <x-candidate-subscription.text
                        name="instituicao_de_ensino"
                        label="Instituição de ensino"
                        :value="old('instituicao_de_ensino')"
                    />
                    <div
                        x-data="{show: false, old: '{{ old('escolaridade', 'no-value') }}'}"
                        x-on:escolaridade.window="if($event.detail == 'ensino superior' || $event.detail == 'ensino técnico'){show = true} else {show = false}"
                    >
                        <template x-if="show || old == 'ensino superior' || old == 'ensino técnico'">
                            <x-candidate-subscription.text
                                name="curso"
                                label="Curso"
                                legend="Digite o curso e o semestre que você está cursando."
                                :value="old('curso')"
                            />
                        </template>
                    </div>

                </div>

            </section>

            <section class="px-2 py-4">

                <h2 class="text-center text-xl lg:text-2xl">Experiência</h2>

                <div class="mt-4 space-y-4 lg:grid lg:grid-cols-2 lg:gap-6 lg:space-y-0">

                    <x-candidate-subscription.radio
                        name="nivel_de_conhecimentos_em_informatica"
                        label="Nível de conhecimentos em informática"
                        :options="App\Models\AprendizForm::NIVEL_INFORMATICA"
                        :value="old('nivel_de_conhecimentos_em_informatica')"
                    />
                    <x-candidate-subscription.checkbox
                        name="conhecimentos_em_informatica[]"
                        label="Conhecimentos em informática"
                        :options="App\Models\AprendizForm::CONHECIMENTOS_INFORMATICA"
                        :value="old('conhecimentos_em_informatica', [])"
                    />

                    <x-candidate-subscription.radio
                        name="possui_cursos_complementares"
                        label="Possui cursos suplementares?"
                        :options="['sim', 'não']"
                        x-data=""
                        x-on:change="$dispatch('cursos-complementares', $event.target.value)"
                        :value="old('possui_cursos_complementares')"
                    />
                    <div
                        class="lg:col-span-2"
                        x-data="{
                            show: {{ old('possui_cursos_complementares', 'false') == 'sim' ? 'true' : 'false' }},
                            quantity: Array.from({ length: {{ App\Models\AprendizForm::parseInputToJson(old('cursos_complementares', 'nada'), 3) }}.length }),
                            old: {{ App\Models\AprendizForm::parseInputToJson(old('cursos_complementares', 'nada'), 3) }},
                            setOldInput(field, index) {
                                return this.old[index][field] || ''
                            },
                            reset() {
                                if (this.show) return
                                this.quantity = Array.from({ length: 1 })
                            }
                        }"
                        @cursos-complementares.window="show = $event.detail == 'sim' ? true : false; reset()"
                    >
                        <template x-if="show">
                            <div>
                                <div class="px-4 py-2 space-y-8 lg:grid lg:grid-cols-3 lg:gap-6 lg:space-y-0">
                                    <template x-for="(value, index) in quantity">
                                        <div class="lg:space-y-2">
                                            <x-candidate-subscription.text
                                                name="cursos_complementares[][nome_do_curso]"
                                                label="Nome do curso"
                                                x-init="$el.lastElementChild.value = setOldInput('Nome do curso', index)"
                                            />
                                            <x-candidate-subscription.text
                                                name="cursos_complementares[][instituicao_do_curso]"
                                                label="Instituição do curso"
                                                x-init="$el.lastElementChild.value = setOldInput('Instituição do curso', index)"
                                            />
                                            <x-candidate-subscription.text
                                                name="cursos_complementares[][duracao_do_curso]"
                                                label="Duração do curso"
                                                x-init="$el.lastElementChild.value = setOldInput('Duração do curso', index)"
                                            />
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

                    <x-candidate-subscription.radio
                        name="possui_experiencia_profissional"
                        label="Possui experiência profissional?"
                        :options="['sim', 'não']"
                        x-data=""
                        x-on:change="$dispatch('experiencia-profissional', $event.target.value)"
                        :value="old('possui_experiencia_profissional')"
                    />
                    <div
                        class="lg:col-span-2"
                        x-data="{
                            show: {{ old('possui_experiencia_profissional', 'false') == 'sim' ? 'true' : 'false' }},
                            quantity: Array.from({ length: {{ App\Models\AprendizForm::parseInputToJson(old('experiencia_profissional', 'nada'), 3) }}.length }),
                            old: {{ App\Models\AprendizForm::parseInputToJson(old('experiencia_profissional', 'nada'), 3) }},
                            setOldInput(field, index) {
                                return this.old[index][field] || ''
                            },
                            reset() {
                                if (this.show) return
                                this.quantity = Array.from({ length: 1 })
                            }
                        }"
                        @experiencia-profissional.window="show = $event.detail == 'sim' ? true : false; reset()"
                    >
                        <template x-if="show">
                            <div>
                                <div class="px-4 py-2 space-y-8 lg:grid lg:grid-cols-3 lg:gap-6 lg:space-y-0">
                                    <template x-for="(value, index) in quantity">
                                        <div class="lg:space-y-2">
                                            <x-candidate-subscription.text
                                                name="experiencia_profissional[][empresa]"
                                                label="Empresa"
                                                x-init="$el.lastElementChild.value = setOldInput('Empresa', index)"
                                            />
                                            <x-candidate-subscription.text
                                                name="experiencia_profissional[][cargo]"
                                                label="Cargo"
                                                x-init="$el.lastElementChild.value = setOldInput('Cargo', index)"
                                            />
                                            <x-candidate-subscription.text
                                                name="experiencia_profissional[][periodo]"
                                                label="Período"
                                                x-init="$el.lastElementChild.value = setOldInput('Período', index)"
                                            />
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

                    <x-candidate-subscription.radio
                        name="esta_empregado"
                        label="Está empregado atualmente?"
                        :options="['sim', 'não']"
                        :value="old('esta_empregado')"
                    />

                </div>

            </section>

            <section class="px-2 py-4">

                <h2 class="text-center text-xl lg:text-2xl">Sobre Você</h2>

                <div class="mt-4 space-y-4 lg:grid lg:grid-cols-2 lg:gap-6 lg:space-y-0">

                    <x-candidate-subscription.textarea
                        class="mt-auto"
                        name="quais_seus_principais_objetivos"
                        label="Quais são seus principais objetivos?"
                        :value="old('quais_seus_principais_objetivos')"
                    />
                    <x-candidate-subscription.textarea
                        name="expectativas_com_o_programa"
                        label="Quais são suas expectativas com o programa jovem aprendiz/estágio:"
                        :value="old('expectativas_com_o_programa')"
                    />

                    <x-candidate-subscription.radio
                        name="comportamento_que_se_identifica"
                        label="Com qual desses comportamentos você mais se identifica?"
                        :options="App\Models\AprendizForm::COMPORTAMENTOS_QUE_SE_IDENTIFICA"
                        :value="old('comportamento_que_se_identifica')"
                    />
                    <x-candidate-subscription.text
                        name="uma_frase"
                        label="Uma frase que resume sua vida:"
                        :value="old('uma_frase')"
                    />

                    <x-candidate-subscription.text
                        name="uma_musica"
                        label="Uma música para se ouvir todos os dias"
                        :value="old('uma_musica')"
                    />
                    <x-candidate-subscription.text
                        name="pode_faltar_tudo_menos"
                        label="Pra você pode faltar tudo, menos..."
                        :value="old('pode_faltar_tudo_menos')"
                    />

                    <x-candidate-subscription.checkbox
                        name="no_espelho_voce_enxerga[]"
                        label="Quando você se olha no espelho, você enxerga:"
                        :options="App\Models\AprendizForm::NO_ESPELHO_ENXERGA"
                        :value="old('no_espelho_voce_enxerga', [])"
                    />
                    <x-candidate-subscription.text
                        name="acrescentaria_na_personalidade"
                        label="O que você acrescentaria na sua personalidade?"
                        :value="old('acrescentaria_na_personalidade')"
                    />

                    <x-candidate-subscription.text
                        name="qual_profissao_gostaria"
                        label="Qual profissão gostaria de ter?"
                        :value="old('qual_profissao_gostaria')"
                    />
                    <x-candidate-subscription.text
                        name="mensagem_para_humanidade"
                        label="Deixe uma mensagem para a humanidade:"
                        :value="old('mensagem_para_humanidade')"
                    />

                    <x-candidate-subscription.select
                        name="como_chegou_ao_ceproesc"
                        label="Como chegou ao Ceproesc?"
                        :options="App\Models\AprendizForm::COMO_CHEGOU_CEPROESC"
                        :value="old('como_chegou_ao_ceproesc')"
                    />

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

                        <p>Autorizo o Ceproesc/Proeaja a utilizar e transferir todas as informações por mim apresentadas no cadastro de forma deliberada, a direcionamento às empresas parceiras do Programa de Inclusão Produtiva desenvolvidos pelas instituições sendo, programa jovem aprendiz e programa de Estágio, para que as mesmas tenham acesso aos meus dados quando dos processos de seleção abertos para possíveis contratações.</p>

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
