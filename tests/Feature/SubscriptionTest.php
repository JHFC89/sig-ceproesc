<?php

namespace Tests\Feature;

use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function creating_the_subscription_form()
    {
        Subscription::createForm();

        $form = Subscription::form();

        $this->assertEquals([
            'Dados Cadastrais',
            'Dados Familiares',
            'Documentação',
            'Escolaridade',
            'Experiência',
            'Sobre Você',
            'Interno',
        ], $form->sections->pluck('name')->toArray());

        $this->assertEquals(63, $form->questions()->count());
    }

    /** @test */
    public function creating_an_entry_for_the_subscription()
    {
        $request = $this->getEntryRequest();

        Subscription::createEntryFromArray($request);

        $this->assertEquals(
            74,
            Subscription::form()->entries->first()->answers()->count()
        );
    }

    private function getEntryRequest()
    {
        return [
            'q1' => 'John Doe',
            'q2' => 'test@test.com',
            'q3' => '1999/12/1',
            'q4' => 'masculino',
            'q5' => 'destro',
            'q6' => 'sp',
            'q7' => 'araraquara',
            'q8' => 'araraquara',
            'q9' => 'rua nove de julho',
            'q10' => 123,
            'q11' => '14.810-100',
            'q12' => 'urbana',
            'q13' => 'vila test',
            'q14' => 'Próximo ao supermercado',
            'q15' => '16 99999 9999',
            'q16' => '16 99999 9999',
            'q17' => 'sim',
            'q18' => 'carro',
            'q19' => 'facebook.com.br',
            'q20' => 'instagram.com.br',
            'q21' => '2',
            'q21-group' => [
                [
                    'q22' => 'Maria Mãe',
                    'q23' => 'Mãe',
                    'q24' => 47,
                    'q25' => 'Empregada doméstica',
                    'q26' => 140000,
                ],
                [
                    'q22' => 'Antonio Pai',
                    'q23' => 'Pai',
                    'q24' => 50,
                    'q25' => 'Dentista',
                    'q26' => 280000,
                ]
            ],
            'q27' => 'Não',
            'q28' => '123.456.789.10',
            'q29' => '987.645.321.00',
            'q30' => 'Marcus Dow',
            'q31' => 'digital',
            'q32' => '12345679',
            'q33' => '123456',
            'q34' => '987654321',
            'q35' => 'dispensado',
            'q36' => '123456789',
            'q37' => 'ensino médio',
            'q38' => 'Completo',
            'q39' => 'Escola Estadual Da Vila',
            'q40' => null,
            'q41' => 'Intermediário',
            'q42-group' => [
                ['q42' => 'word'],
                ['q42' => 'excel'],
            ],
            'q43' => 'Sim',
            'q43-group' => [
                [
                    'q44' => 'Curso de capacitação bem legal',
                    'q45' => 'Uniara',
                    'q46' => '6 meses',
                ],
                [
                    'q44' => 'Curso de capacitação super massa',
                    'q45' => 'Unip',
                    'q46' => '6 anos',
                ]
            ],
            'q47' => 'Sim',
            'q47-group' => [
                [
                    'q48' => 'Ariete Web',
                    'q49' => 'Servidor de café',
                    'q50' => 'Julho a agosto de 2021',
                ],
                [
                    'q48' => 'Cutralle',
                    'q49' => 'Mecânico',
                    'q50' => '3 anos',
                ]
            ],
            'q51' => 'sim',
            'q52' => 'Ser um empregado bom',
            'q53' => 'Aprender muita coisa legal',
            'q54' => 'analítico',
            'q55' => 'Tudo é nada, nada é massa',
            'q56' => 'Undertow',
            'q57' => 'Dinheiro',
            'q58-group' => [
                ['q58' => 'superação'],
                ['q58' => 'alegria'],
            ],
            'q59' => 'Minha conta bancária',
            'q60' => 'Astronauta',
            'q61' => 'Liberdade sempre',
            'q62' => 'Instagram',
            'q63' => null,
        ];
    }
}
