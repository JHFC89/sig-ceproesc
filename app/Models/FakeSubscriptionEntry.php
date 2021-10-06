<?php

namespace App\Models;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class FakeSubscriptionEntry
{
    use WithFaker;

    public function __construct()
    {
        $this->faker = $this->makeFaker('pt_BR');
    }

    protected function faker($locale = null)
    {
        return is_null($locale) ? $this->faker : $this->makeFaker($locale);
    }

    public function createMany($quantity)
    {
        for ($i = 0; $i < $quantity; $i++) {
            Subscription::createEntryFromArray($this->createEntryArray());
        }
    }

    private function createEntryArray(array $overrides = [])
    {
        $q1 = Str::of($this->faker()->name())->remove(['Dr. ', 'Srta. ', 'Sr. ', 'Sra ']);
        $q2 = $this->faker()->email();
        $q3 = $this->birthdate();
        $q4 = $this->randomAnswer('gender');
        $q5 = $this->randomAnswer('hand');
        $q6 = $this->randomAnswer('state');
        $q7 = $this->randomAnswer('city');
        $q8 = $this->randomAnswer('city');
        $q9 = $this->faker()->streetName();
        $q10 = $this->faker()->numberBetween(100, 9999);
        $q11 = $this->cep();
        $q12 = $this->randomAnswer('zona');
        $q13 = $this->randomAnswer('bairro');
        $q14 = $this->randomAnswer('complemento');
        $q15 = $this->cellphone();
        $q16 = $this->cellphone();
        $q17 = $this->randomAnswer('yesno');
        $q18 = $this->randomAnswer('driver');
        $q19 = 'facebook.com.br';
        $q20 = 'instagram.com.br';
        $q21 = $this->faker()->numberBetween(1, 3);

        $q21_group = [];
        for ($i = 1; $i <= $q21; $i++) {
            $q21_group[$i - 1]['q22'] = Str::of($this->faker()->name())->remove(['Dr. ', 'Srta. ', 'Sr. ', 'Sra ']);
            $q21_group[$i - 1]['q23'] = $this->randomAnswer('parentesco');
            $q21_group[$i - 1]['q24'] = $this->faker()->numberBetween(40, 60);
            $q21_group[$i - 1]['q25'] = $this->randomAnswer('emprego');
            $q21_group[$i - 1]['q26'] = 'R$ ' . $this->randomAnswer('renda') . ',00';
        }

        $q27 = $this->randomAnswer('yesno');
        $q28 = $this->cpf();
        $q29 = $this->cpf();
        $q30 = $q21_group[0]['q22'];
        $q31 = 'Digital';
        $q32 = $this->faker()->numberBetween(10000, 99999);
        $q33 = $this->rg();
        $q34 = $this->faker()->biasedNumberBetween(10000, 99999);

        if ($q4 == 'masculino') {
            $q35 = $this->randomAnswer('alistamento');
            $q36 = '123456789';
        } else {
            $q35 = null;
            $q36 = null;
        }

        $q37 = $this->randomAnswer('escolaridade');
        $q38 = $this->randomAnswer('conclusão');
        $q39 = $this->randomAnswer('instituição');
        $q40 = $this->randomAnswer('curso');
        $q41 = $this->randomAnswer('nivel');

        $q42_group = [];
        for ($i = 0; $i <= $this->faker()->numberBetween(0, 2); $i++) {
            $q42_group[$i]['q42'] = $this->randomAnswer('informática');
        }

        $q43 = $this->randomAnswer('yesno');

        $q43_group = [];
        if ($q43 == 'sim') {
            for ($i = 0; $i <= $this->faker()->numberBetween(0, 2); $i++) {
                $q43_group[$i]['q44'] = $this->randomAnswer('cursos');
                $q43_group[$i]['q45'] = $this->randomAnswer('instituição');
                $q43_group[$i]['q46'] = $this->faker()->numberBetween(1, 36) . ' meses';
            }
        } else {
            $q43_group[0]['q44'] = null;
            $q43_group[0]['q45'] = null;
            $q43_group[0]['q46'] = null;
        }

        $q47 = $this->randomAnswer('yesno');

        $q47_group = [];
        if ($q47 == 'sim') {
            for ($i = 0; $i <= $this->faker()->numberBetween(0, 2); $i++) {
                $q47_group[$i]['q48'] = $this->randomAnswer('empresa');
                $q47_group[$i]['q49'] = $this->randomAnswer('emprego');
                $q47_group[$i]['q50'] = $this->faker()->numberBetween(1, 36) . ' meses';
            }
        } else {
            $q47_group[0]['q48'] = null;
            $q47_group[0]['q49'] = null;
            $q47_group[0]['q50'] = null;
        }

        $q51 = $this->randomAnswer('yesno');
        $q52 = $this->faker()->realText(10, 2);
        $q53 = $this->faker()->realText(20, 2);
        $q54 = $this->randomAnswer('comportamentos');
        $q55 = $this->faker()->realText(20, 2);
        $q56 = $this->faker()->realText(10, 2);
        $q57 = $this->randomAnswer('faltar');

        $q58_group = [];
        for ($i = 0; $i <= $this->faker()->numberBetween(0, 5); $i++) {
            $q58_group[$i]['q58'] = $this->randomAnswer('espelho');
        }

        $q59 = $this->randomAnswer('personalidade');
        $q60 = $this->randomAnswer('emprego');
        $q61 = $this->faker()->realText(25, 2);
        $q62 = $this->randomAnswer('chegou');
        $q63 = $this->faker()->realText(140, 2);

        $answers = [
            'q1' => $q1,
            'q2' => $q2,
            'q3' => $q3,
            'q4' => $q4,
            'q5' => $q5,
            'q6' => $q6,
            'q7' => $q7,
            'q8' => $q8,
            'q9' => $q9,
            'q10' => $q10,
            'q11' => $q11,
            'q12' => $q12,
            'q13' => $q13,
            'q14' => $q14,
            'q15' => $q15,
            'q16' => $q16,
            'q17' => $q17,
            'q18' => $q18,
            'q19' => $q19,
            'q20' => $q20,
            'q21' => $q21,
            'q21_group' => $q21_group,
            'q27' => $q27,
            'q28' => $q28,
            'q29' => $q29,
            'q30' => $q30,
            'q31' => $q31,
            'q32' => $q32,
            'q33' => $q33,
            'q34' => $q34,
            'q35' => $q35,
            'q36' => $q36,
            'q37' => $q37,
            'q38' => $q38,
            'q39' => $q39,
            'q40' => $q40,
            'q41' => $q41,
            'q42_group' => $q42_group,
            'q43' => $q43,
            'q43_group' => $q43_group,
            'q47' => $q47,
            'q47_group' => $q47_group,
            'q51' => $q51,
            'q52' => $q52,
            'q53' => $q53,
            'q54' => $q54,
            'q55' => $q55,
            'q56' => $q56,
            'q57' => $q57,
            'q58_group' => $q58_group,
            'q59' => $q59,
            'q60' => $q60,
            'q61' => $q61,
            'q62' => $q62,
            'q63' => $q63,
        ];

        return array_merge($answers, $overrides);
    }

    private function birthdate()
    {
        return implode('/', [
            $this->faker()->numberBetween(1999, 2007),
            $this->faker()->numberBetween(1, 12),
            $this->faker()->numberBetween(1, 28),
        ]);
    }

    private function cep()
    {
        $a = $this->faker()->numberBetween(10, 99);
        $b = $this->faker()->numberBetween(100, 999);
        $c = $this->faker()->numberBetween(100, 999);

        return "${a}.${b}-${c}";
    }

    private function cellphone()
    {
        $a = $this->faker()->numberBetween(10, 99);
        $b = $this->faker()->numberBetween(1000, 9999);
        $c = $this->faker()->numberBetween(1000, 9999);

        return "(${a}) 9${b}-${c}";
    }

    private function cpf()
    {
        $a = $this->faker()->numberBetween(100, 999);
        $b = $this->faker()->numberBetween(100, 999);
        $c = $this->faker()->numberBetween(100, 999);
        $d = $this->faker()->numberBetween(10, 99);

        return "${a}.${b}.${c}-${d}";
    }

    private function rg()
    {
        $a = $this->faker()->numberBetween(10, 99);
        $b = $this->faker()->numberBetween(100, 999);
        $c = $this->faker()->numberBetween(100, 999);
        $s = $this->randomAnswer('state');

        return "${a}-${b}-${c}/${s}";
    }

    private function randomAnswer($type)
    {
        return $this->random($this->answers()[$type]);
    }

    private function random($array)
    {
        return array_rand(array_flip($array));
    }

    private function answers()
    {
        return [
            'gender' => ['masculino', 'feminino'],
            'hand'   => ['destro', 'canhoto'],
            'state'   => $this->getStates(),
            'city'   => ['Araraquara', 'Matão', 'São Carlos', 'Ribeirão Preto', 'Américo Brasiliense', 'Ibaté'],
            'zona'   => ['urbana', 'rural'],
            'bairro'   => ['Vila Xavier', 'Vila Harmonia', 'Parque Bonito', 'Jardim Primavera', 'Centro'],
            'yesno'   => ['sim', 'não'],
            'driver'   => ['carro', 'moto', 'ambos', 'nenhum'],
            'escolaridade'   => ['ensino fundamental', 'ensino médio', 'ensino superior'],
            'curso'   => ['direito', 'nutrição', 'letras', 'administração', 'estética', 'engenharia'],
            'cursos'   => ['curso de word', 'curso de mecânico', 'curso de cálculo', 'curso de enfermagem', 'curso de matemática', 'curso de redação empresarial'],
            'complemento'   => ['próximo ao mercado', 'próximo à padaria', 'perto do hospital São Vicente', 'perto do Bar do Zé', 'próximo da estação Sé', 'perto do condomínio Azul'],
            'parentesco'   => ['mãe', 'pai', 'irmão', 'irmã', 'tia', 'vó'],
            'renda'   => ['1.700', '1.500', '2.000', '700', '2.300', '1.200'],
            'alistamento'   => ['dispensado', 'não dispensado', 'ainda não convocado'],
            'conclusão'   => ['completo', 'cursando'],
            'instituição'   => ['escola Maria Márcia', 'Faculdade Aprendizado', 'Escola Estatual de SP', 'Uniara', 'Usp', 'Escola Dr. Eurípedes Araújo'],
            'nivel'   => ['básico', 'intermediário', 'avançado'],
            'informática'   => ['word', 'excel', 'power point'],
            'comportamentos'   => ['executor', 'comunicador', 'planejador', 'analítico'],
            'faltar'   => ['amor', 'saúde', 'paz', 'dinheiro', 'alegria', 'trabalho', 'amigos', 'família', 'alimentos'],
            'espelho'   => ['alegria', 'superação', 'desespero', 'beleza', 'angustia', 'medo', 'tristeza', 'coragem', 'determinação', 'fé'],
            'personalidade'   => ['bom humor', 'paciência', 'mais tranquilidade', 'tranquilidade', 'mais paciência', 'mais extroversão'],
            'chegou'   => ['facebook', 'instagram', 'linkedin', 'google', 'portal', 'indicação de empresas', 'indicação de amigos', 'indicação de familiares', 'youtube'],
            'empresa'   => ['empresa modelo', 'empresa exemplo', 'cutrale', 'lupo', 'nigro', 'ambev'],
            'emprego'   => ['ajudante', 'auxiliar', 'secretária', 'encarregado', 'gerente', 'bombeiro', 'mecânico', 'motorista'],
        ];
    }

    private function getQuestions()
    {
        return [
            'dados cadastrais' => [
                [
                    'content'   => 'Nome',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'E-mail',
                    'type'      => 'text',
                    'rules'     => ['string', 'email', 'max:255'],
                ],
                [
                    'content'   => 'Data de nascimento',
                    'type'      => 'date',
                    'rules'     => ['date'],
                ],
                [
                    'content'   => 'Gênero',
                    'type'      => 'radio',
                    'rules'     => ['string'],
                    'options'   => [
                        'options'   => ['masculino', 'feminino'],
                        'test'      => '3'
                    ],
                ],
                [
                    'content'   => 'Habilidade manual',
                    'type'      => 'radio',
                    'rules'     => ['string'],
                    'options'   => ['destro', 'canhoto'],
                ],
                [
                    'content'   => 'Estado de naturalidade',
                    'type'      => 'select',
                    'rules'     => ['string', 'size:2'],
                    'options'   => $this->getStates(),
                ],
                [
                    'content'   => 'Cidade onde nasceu',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Cidade onde mora',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Logradouro',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Número',
                    'type'      => 'number',
                    'rules'     => ['integer'],
                ],
                [
                    'content'   => 'CEP',
                    'type'      => 'text',
                    'rules'     => ['string', 'size:10'],
                ],
                [
                    'content'   => 'Zona',
                    'type'      => 'radio',
                    'rules'     => ['string'],
                    'options'   => ['urbana', 'rural'],
                ],
                [
                    'content'   => 'Bairro',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Complemento',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Telefone do candidato',
                    'type'      => 'text',
                    'rules'     => ['string', 'between:14,15'],
                ],
                [
                    'content'   => 'Telefone de recado',
                    'type'      => 'text',
                    'rules'     => ['string', 'between:14,15'],
                ],
                [
                    'content'   => 'Carteira de habilitação',
                    'type'      => 'radio',
                    'rules'     => ['string'],
                    'options'   => ['sim', 'não'],
                ],
                [
                    'content'   => 'Categoria',
                    'type'      => 'radio',
                    'rules'     => ['string'],
                    'options'   => ['carro', 'moto', 'ambos'],
                ],
                [
                    'content'   => 'Facebook',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Instagram',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
            ],
            'dados familiares' => [
                [
                    'content'   => 'Quantas pessoas moram com você?',
                    'type'      => 'number',
                    'rules'     => ['integer'],
                ],
                [
                    'content'   => 'Nome do morador',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Parentesco',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Idade do morador',
                    'type'      => 'number',
                    'rules'     => ['integer'],
                ],
                [
                    'content'   => 'Ocupação',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Renda',
                    'type'      => 'number',
                    'rules'     => ['integer'],
                ],
                [
                    'content'   => 'A família recebe algum auxílio do governo?',
                    'type'      => 'radio',
                    'rules'     => ['string'],
                    'options'   => ['sim', 'não'],
                ],
            ],
            'documentação' => [
                [
                    'content'   => 'CPF do candidato',
                    'type'      => 'text',
                    'rules'     => ['string', 'size:14'],
                ],
                [
                    'content'   => 'CPF do responsável',
                    'type'      => 'text',
                    'rules'     => ['string', 'size:14'],
                ],
                [
                    'content'   => 'Nome do responsável',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Carteira de trabalho',
                    'type'      => 'radio',
                    'rules'     => ['string'],
                    'options'   => ['digital', 'física'],
                ],
                [
                    'content'   => 'Número de série',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'RG/UF',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Título de eleitor',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Alistamento militar',
                    'type'      => 'radio',
                    'rules'     => ['string'],
                    'options'   => ['dispensado', 'não dispensado', 'ainda não convocado'],
                ],
                [
                    'content'   => 'Número de reservista',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
            ],
            'escolaridade' => [
                [
                    'content'   => 'escolaridade',
                    'type'      => 'radio',
                    'rules'     => ['string'],
                    'options'   => ['ensino fundamental', 'ensino médio', 'ensino superior'],
                ],
                [
                    'content'   => 'conclusão',
                    'type'      => 'radio',
                    'rules'     => ['string'],
                    'options'   => ['completo', 'cursando'],
                ],
                [
                    'content'   => 'Instituição de ensino',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Curso',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
            ],
            'experiência' => [
                [
                    'content'   => 'Nível de conhecimentos em informática',
                    'type'      => 'radio',
                    'rules'     => ['string', 'max:255'],
                    'options'   => ['básico', 'intermediário', 'avançado'],
                ],
                [
                    'content'   => 'Conhecimentos em informática',
                    'type'      => 'checkbox',
                    'rules'     => ['array'],
                    'options'   => ['excel', 'word', 'power point'],
                ],
                [
                    'content'   => 'Possui alguma capacitação?',
                    'type'      => 'radio',
                    'rules'     => ['string'],
                    'options'   => ['sim', 'não'],
                ],
                [
                    'content'   => 'Nome do curso',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Instituição do curso',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Duração do curso',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Possui experiência profissional?',
                    'type'      => 'radio',
                    'rules'     => ['string'],
                    'options'   => ['sim', 'não'],
                ],
                [
                    'content'   => 'Empresa',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Cargo',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Período',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
            ],
            'sobre você' => [
                [
                    'content'   => 'Quais seus principais objetivos?',
                    'type'      => 'textarea',
                    'rules'     => ['string'],
                ],
                [
                    'content'   => 'Expectativas com o programa jovem aprendiz:',
                    'type'      => 'textarea',
                    'rules'     => ['string'],
                ],
                [
                    'content'   => 'O estudo para você é uma escolha pessoal ou uma oportunidade que muitas vezes é difícil acessar?',
                    'type'      => 'text',
                    'rules'     => ['string'],
                ],
                [
                    'content'   => 'Uma frase que resume sua vida:',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Uma música para se ouvir todos os dias',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Pra você pode faltar tudo, menos...',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Quando você se olha no espelho, você enxerga:',
                    'type'      => 'checkbox',
                    'rules'     => ['array'],
                    'options'   => ['alegria', 'superação', 'desespero', 'beleza', 'angustia', 'medo', 'tristeza', 'coragem', 'determinação', 'fé'],
                ],
                [
                    'content'   => 'O que você mudaria em você?',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Qual profissão gostaria de ter?',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Deixe uma mensagem para humanidade:',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Como chegou ao Ceproesc?',
                    'type'      => 'select',
                    'rules'     => ['array'],
                    'options'   => ['facebook', 'instagram', 'linkedin', 'google', 'portal', 'indicação de empresas', 'indicação de amigos', 'indicação de familiares', 'youtube'],
                ],
            ],
        ];
    }

    private function getStates()
    {
        return ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
    }
}
