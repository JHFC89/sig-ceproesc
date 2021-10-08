<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MattDaneshvar\Survey\Models\Survey;
use MattDaneshvar\Survey\Models\Answer;

class Subscription extends Model
{
    use HasFactory;

    static function createEntryFromArray(array $array, $entry = null)
    {
        $form = self::form();

        if (empty($entry)) {
            $entry = $form->entries()->make();
            $entry->cpf = $array['q28'];
            $entry->save();
        }

        foreach ($array as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (is_iterable($value)) {
                foreach ($value as $group) {
                    foreach ($group as $key => $value) {
                        if ($value === null) {
                            continue;
                        }

                        $entry->answers->add(Answer::make([
                            'question_id'   => substr($key, 1),
                            'entry_id'      => $entry->id,
                            'value'         => $value,
                        ]));
                    }
                }
            } else {
                $entry->answers->add(Answer::make([
                    'question_id'   => substr($key, 1),
                    'entry_id'      => $entry->id,
                    'value'         => $value,
                ]));
            }
        }

        $entry->push();

        return $entry->refresh();
    }

    static function mapQuestionsKeyToContent()
    {
        return self::form()->questions->mapWithKeys(function ($question) {
            return [$question->key => $question->content];
        });
    }

    static function groups()
    {
        return [
            'q21' => ['q22', 'q23', 'q24', 'q25', 'q26'],
            'q42' => 'q42',
            'q43' => ['q44', 'q45', 'q46'],
            'q47' => ['q48', 'q49', 'q50'],
            'q58' => 'q58',
        ];
    }

    static function form()

    {
        return Survey::where('name', 'subscription form')->first()
            ?? self::createForm();
    }

    static public function createForm()
    {
        if (Survey::count() > 0) {
            return self::form();
        }

        $form = Survey::create(['name' => 'subscription form', 'settings' => ['accept-guest-entries' => true]]);

        $dados_cadastrais = $form->sections()->create(['name' => 'Dados Cadastrais']);
        $dados_cadastrais->questions()->createMany(self::getQuestions()['dados cadastrais']);

        $dados_familiares = $form->sections()->create(['name' => 'Dados Familiares']);
        $dados_familiares->questions()->createMany(self::getQuestions()['dados familiares']);

        $documentacao = $form->sections()->create(['name' => 'Documentação']);
        $documentacao->questions()->createMany(self::getQuestions()['documentação']);

        $escolaridade = $form->sections()->create(['name' => 'Escolaridade']);
        $escolaridade->questions()->createMany(self::getQuestions()['escolaridade']);

        $experiencia = $form->sections()->create(['name' => 'Experiência']);
        $experiencia->questions()->createMany(self::getQuestions()['experiência']);

        $sobre_voce = $form->sections()->create(['name' => 'Sobre Você']);
        $sobre_voce->questions()->createMany(self::getQuestions()['sobre você']);

        $interno = $form->sections()->create(['name' => 'Interno']);
        $interno->questions()->createMany(self::getQuestions()['interno']);

        return $form->fresh();
    }

    static private function getQuestions()
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
                    'type'      => 'email',
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
                    'rules'     => ['string', 'max:20'],
                    'options'   => ['masculino', 'feminino'],
                ],
                [
                    'content'   => 'Habilidade manual',
                    'type'      => 'radio',
                    'rules'     => ['string', 'max:20'],
                    'options'   => ['destro', 'canhoto'],
                ],
                [
                    'content'   => 'Estado de naturalidade',
                    'type'      => 'select',
                    'rules'     => ['string', 'size:2'],
                    'options'   => self::states(),
                ],
                [
                    'content'   => 'Cidade onde nasceu',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:100'],
                ],
                [
                    'content'   => 'Cidade onde mora',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:100'],
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
                    'rules'     => ['string', 'max:25'],
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
                    'rules'     => ['string', 'max:10', 'in:sim,não'],
                    'options'   => ['sim', 'não'],
                ],
                [
                    'content'   => 'Categoria',
                    'type'      => 'radio',
                    'rules'     => ['string', 'max:20'],
                    'options'   => ['carro', 'moto', 'ambos', 'nenhum'],
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
                    'rules'     => ['integer', 'max:150'],
                ],

                [
                    'content'   => 'Ocupação',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Renda',
                    'type'      => 'text',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'A família recebe algum auxílio do governo?',
                    'type'      => 'radio',
                    'rules'     => ['string', 'max:10', 'in:sim,não'],
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
                    'rules'     => ['string', 'max:20'],
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
                    'rules'     => ['string', 'max:50'],
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
                    'content'   => 'Escolaridade',
                    'type'      => 'radio',
                    'rules'     => ['string', 'max:50'],
                    'options'   => ['ensino fundamental', 'ensino médio', 'ensino superior'],
                ],
                [
                    'content'   => 'Situação',
                    'type'      => 'radio',
                    'rules'     => ['string', 'max:20'],
                    'options'   => ['completo', 'cursando', 'trancado'],
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
                    'rules'     => ['string', 'max:255'],
                    'options'   => ['word', 'excel', 'power point', 'nenhum'],
                ],
                [
                    'content'   => 'Possui cursos complementares?',
                    'type'      => 'radio',
                    'rules'     => ['string', 'max:10', 'in:sim,não'],
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
                    'rules'     => ['string', 'max:10', 'in:sim,não'],
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
                [
                    'content'   => 'Está empregado atualmente?',
                    'type'      => 'radio',
                    'rules'     => ['string', 'max:10', 'in:sim,não'],
                    'options'   => ['sim', 'não'],
                ],
            ],
            'sobre você' => [
                [
                    'content'   => 'Quais seus principais objetivos?',
                    'type'      => 'textarea',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Quais são suas expectativas com o programa jovem aprendiz:',
                    'type'      => 'textarea',
                    'rules'     => ['string', 'max:255'],
                ],
                [
                    'content'   => 'Com qual desses comportamentos você mais se identifica?',
                    'type'      => 'radio',
                    'rules'     => ['string', 'max:50'],
                    'options'   => ['executor', 'comunicador', 'planejador', 'analítico'],
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
                    'rules'     => ['string', 'max:255'],
                    'options'   => ['alegria', 'superação', 'desespero', 'beleza', 'angustia', 'medo', 'tristeza', 'coragem', 'determinação', 'fé'],
                ],
                [
                    'content'   => 'O que você acrescentaria na sua personalidade?',
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
                    'rules'     => ['string', 'max:255'],
                    'options'   => ['facebook', 'instagram', 'linkedin', 'google', 'portal', 'indicação de empresas', 'indicação de amigos', 'indicação de familiares', 'youtube'],
                ],
            ],
            'interno' => [
                [
                    'content'   => 'Histórico',
                    'type'      => 'textarea',
                    'rules'     => ['string', 'max:255'],
                ],
            ]
        ];
    }

    static private function states()
    {
        return ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
    }
}
