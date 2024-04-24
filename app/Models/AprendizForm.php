<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MattDaneshvar\Survey\Models\{Entry, Answer};

class AprendizForm extends Model
{
    use HasFactory;

    public const GENEROS = ['masculino', 'feminino'];
    public const HABILIDADES_MANUAIS = ['destro', 'canhoto'];
    public const ESTADOS = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
    public const ZONAS = ['urbana', 'rural'];
    public const CATEGORIAS_CNH = ['carro', 'moto', 'ambos', 'nenhum'];
    public const CARTEIRA_DE_TRABALHO = ['digital', 'física'];
    public const ALISTAMENTO_MILITAR = ['dispensado', 'não dispensado', 'ainda não convocado'];
    public const ESCOLARIDADE = ['ensino fundamental', 'ensino médio', 'ensino superior', 'ensino técnico'];
    public const SITUACAO_ESCOLARIDADE = ['completo', 'cursando', 'trancado'];
    public const NIVEL_INFORMATICA = ['básico', 'intermediário', 'avançado'];
    public const CONHECIMENTOS_INFORMATICA = ['word', 'excel', 'power point', 'nenhum'];
    public const COMPORTAMENTOS_QUE_SE_IDENTIFICA = ['executor', 'comunicador', 'planejador', 'analítico'];
    public const NO_ESPELHO_ENXERGA = ['alegria', 'superação', 'desespero', 'beleza', 'angustia', 'medo', 'tristeza', 'coragem', 'determinação', 'fé'];
    public const COMO_CHEGOU_CEPROESC = ['facebook', 'instagram', 'linkedin', 'google', 'portal', 'indicação de empresas', 'indicação de amigos', 'indicação de familiares', 'youtube'];
    public const QUESTIONS_TO_FIELDS_MAP = [
        1 => 'nome',
        2 => 'email',
        3 => 'data_de_nascimento',
        4 => 'genero',
        5 => 'habilidade_manual',
        6 => 'estado_de_naturalidade',
        7 => 'cidade_onde_nasceu',
        8 => 'cidade_onde_mora',
        9 => 'logradouro',
        10 => 'numero',
        11 => 'cep',
        12 => 'zona',
        13 => 'bairro',
        14 => 'complemento',
        15 => 'telefone',
        16 => 'telefone_de_recado',
        17 => 'carteira_de_habilitação',
        18 => 'categoria',
        19 => 'facebook',
        20 => 'instagram',
        21 => 'quantas_pessoas_moram_com_voce',
        22 => 'moradores', // agrupamento 1 -> 23, 24, 25 e 26
        27 => 'a_familia_recebe_algum_auxilio_do_governo',
        28 => 'cpf',
        29 => 'cpf_do_responsavel',
        30 => 'nome_do_responsavel',
        31 => 'carteira_de_trabalho',
        32 => 'numero_de_serie',
        33 => 'rg',
        34 => 'titulo_de_eleitor',
        35 => 'alistamento_militar',
        36 => 'numero_de_reservista',
        37 => 'escolaridade',
        38 => 'situacao_escolaridade',
        39 => 'instituicao_de_ensino',
        40 => 'curso',
        41 => 'nivel_de_conhecimentos_em_informatica',
        42 => 'conhecimentos_em_informatica', // array
        43 => 'possui_cursos_complementares',
        44 => 'cursos_complementares', // agrupamento 2 -> 44, 45 e 46
        47 => 'possui_experiencia_profissional',
        48 => 'experiencia_profissional', // agrupamento 3 -> 48, 49 e 50
        51 => 'esta_empregado',
        52 => 'quais_seus_principais_objetivos',
        53 => 'expectativas_com_o_programa',
        54 => 'comportamento_que_se_identifica',
        55 => 'uma_frase',
        56 => 'uma_música',
        57 => 'pode_faltar_tudo_menos',
        58 => 'no_espelho_voce_enxerga', // array
        59 => 'acrescentaria_na_personalidade',
        60 => 'qual_profissao_gostaria',
        61 => 'mensagem_para_humanidade',
        62 => 'como_chegou_ao_ceproesc',
        63 => 'historico',
    ];

    public function getSection($name)
    {
        $sections = [
            'dados cadastrais' => [
                'nome' => $this->nome,
                'email' => $this->email,
                'data_de_nascimento' => $this->data_de_nascimento,
                'genero' => $this->genero,
                'habilidade_manual' => $this->habilidade_manual,
                'estado_de_naturalidade' => $this->estado_de_naturalidade,
                'cidade_onde_nasceu' => $this->nome,
                'cidade_onde_mora' => $this->cidade_onde_mora,
                'logradouro' => $this->logradouro,
                'numero' => $this->numero,
                'cep' => $this->cep,
                'zona' => $this->zona,
                'bairro' => $this->bairro,
                'complemento' => $this->complemento,
                'telefone' => $this->telefone,
                'telefone_de_recado' => $this->telefone_de_recado,
                'carteira_de_habilitação' => $this->carteira_de_habilitação,
                'categoria' => $this->categoria,
                'facebook' => $this->facebook,
                'instagram' => $this->instagram,
            ],
            'dados familiares' => [
                'quantas_pessoas_moram_com_voce' => $this->quantas_pessoas_moram_com_voce,
                'moradores' => $this->moradores,
                'a_familia_recebe_algum_auxilio_do_governo' => $this->a_familia_recebe_algum_auxilio_do_governo,
            ],
            'documentação' => [
                'cpf' => $this->cpf,
                'cpf_do_responsavel' => $this->cpf_do_responsavel,
                'nome_do_responsavel' => $this->nome_do_responsavel,
                'carteira_de_trabalho' => $this->carteira_de_trabalho,
                'numero_de_serie' => $this->numero_de_serie,
                'rg' => $this->rg,
                'titulo_de_eleitor' => $this->titulo_de_eleitor,
                'alistamento_militar' => $this->alistamento_militar,
                'numero_de_reservista' => $this->numero_de_reservista,
            ],
            'escolaridade' => [
                'escolaridade' => $this->escolaridade,
                'situacao_escolaridade' => $this->situacao_escolaridade,
                'instituicao_de_ensino' => $this->instituicao_de_ensino,
                'curso' => $this->curso,
            ],
            'experiência' => [
                'nivel_de_conhecimentos_em_informatica' => $this->nivel_de_conhecimentos_em_informatica,
                'conhecimentos_em_informatica' => $this->conhecimentos_em_informatica,
                'possui_cursos_complementares' => $this->possui_cursos_complementares,
                'cursos_complementares' => $this->cursos_complementares,
                'possui_experiencia_profissional' => $this->possui_experiencia_profissional,
                'experiencia_profissional' => $this->experiencia_profissional,
                'esta_empregado' => $this->esta_empregado,
            ],
            'sobre você' => [
                'quais_seus_principais_objetivos' => $this->quais_seus_principais_objetivos,
                'expectativas_com_o_programa' => $this->expectativas_com_o_programa,
                'comportamento_que_se_identifica' => $this->comportamento_que_se_identifica,
                'uma_frase' => $this->uma_frase,
                'uma_música' => $this->uma_música,
                'pode_faltar_tudo_menos' => $this->pode_faltar_tudo_menos,
                'no_espelho_voce_enxerga' => $this->no_espelho_voce_enxerga,
                'acrescentaria_na_personalidade' => $this->acrescentaria_na_personalidade,
                'qual_profissao_gostaria' => $this->qual_profissao_gostaria,
                'mensagem_para_humanidade' => $this->mensagem_para_humanidade,
                'como_chegou_ao_ceproesc' => $this->como_chegou_ao_ceproesc,
            ],
            'interno' => [
                'historico' => $this->historico,
            ],
        ];

        return $sections[$name];
    }

    public function getTitle($field)
    {
        $titles = [
            'nome' => 'Nome',
            'email' => 'E-mail',
            'data_de_nascimento' => 'Data de nascimento',
            'genero' => 'Gênero',
            'habilidade_manual' => 'Habilidade manual',
            'estado_de_naturalidade' => 'Estado de naturalidade',
            'cidade_onde_nasceu' => 'Cidade onde nasceu',
            'cidade_onde_mora' => 'Cidade onde mora',
            'logradouro' => 'Logradouro',
            'numero' => 'Número',
            'cep' => 'CEP',
            'zona' => 'Zona',
            'bairro' => 'Bairro',
            'complemento' => 'Complemento',
            'telefone' => 'Telefone do candidato',
            'telefone_de_recado' => 'Telefone de recado',
            'carteira_de_habilitação' => 'Carteira de habilitação',
            'categoria' => 'Categoria',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'quantas_pessoas_moram_com_voce' => 'Quantas pessoas moram com você?',
            'moradores' => 'Moradores',
            'a_familia_recebe_algum_auxilio_do_governo' => 'A família recebe algum auxílio do governo?',
            'cpf' => 'CPF do candidato',
            'cpf_do_responsavel' => 'CPF do responsável',
            'nome_do_responsavel' => 'Nome do responsável',
            'carteira_de_trabalho' => 'Carteira de trabalho',
            'numero_de_serie' => 'Número de série',
            'rg' => 'RG/UF',
            'titulo_de_eleitor' => 'Título de eleitor',
            'alistamento_militar' => 'Alistamento militar',
            'numero_de_reservista' => 'Número de reservista',
            'escolaridade' => 'Escolaridade',
            'situacao_escolaridade' => 'Situação',
            'instituicao_de_ensino' => 'Instituição de ensino',
            'curso' => 'Curso',
            'nivel_de_conhecimentos_em_informatica' => 'Nível de conhecimentos em informática',
            'conhecimentos_em_informatica' => 'Conhecimentos em informática',
            'possui_cursos_complementares' => 'Possui cursos complementares?',
            'cursos_complementares' => 'Cursos complementares',
            'possui_experiencia_profissional' => 'Possui experiência profissional?',
            'experiencia_profissional' => 'Experiência profissional?',
            'esta_empregado' => 'Está empregado atualmente?',
            'quais_seus_principais_objetivos' => 'Quais seus principais objetivos?',
            'expectativas_com_o_programa' => 'Quais são suas expectativas com o programa jovem aprendiz:',
            'comportamento_que_se_identifica' => 'Com qual desses comportamentos você mais se identifica?',
            'uma_frase' => 'Uma frase que resume sua vida:',
            'uma_música' => 'Uma música para se ouvir todos os dias',
            'pode_faltar_tudo_menos' => 'Pra você pode faltar tudo, menos...',
            'no_espelho_voce_enxerga' => 'Quando você se olha no espelho, você enxerga:',
            'acrescentaria_na_personalidade' => 'O que você acrescentaria na sua personalidade?',
            'qual_profissao_gostaria' => 'Qual profissão gostaria de ter?',
            'mensagem_para_humanidade' => 'Deixe uma mensagem para humanidade:',
            'como_chegou_ao_ceproesc' => 'Como chegou ao Ceproesc?',
            'historico' => 'Histórico',
        ];

        return $titles[$field];
    }

    public static function importFromOldModel(Entry $entry)
    {
        $form = new Self();
        $emptyJsonArray = json_encode([]);

        $form->moradores = $emptyJsonArray;
        $moradores = [];

        $form->conhecimentos_em_informatica = $emptyJsonArray;

        $form->cursos_complementares = $emptyJsonArray;
        $cursos_complementares = [];

        $form->experiencia_profissional = $emptyJsonArray;
        $experiencia_profissional = [];

        $form->no_espelho_voce_enxerga = $emptyJsonArray;

        $entry->answers->each(function (Answer $answer) use ($form, &$moradores, &$cursos_complementares, &$experiencia_profissional) {
            $qId = $answer->question->id;

            if (in_array($qId, [22, 23, 24, 25, 26])) {
                $save = false;
                switch ($qId) {
                    case 22:
                        $key = 'Nome do morador';
                        break;
                    case 23:
                        $key = 'Parentesco';
                        break;
                    case 24:
                        $key = 'Idade do morador';
                        break;
                    case 25:
                        $key = 'Ocupação';
                        break;
                    case 26:
                        $key = 'Renda';
                        $save = true;
                        break;
                }
                $moradores[$key] = $answer->value;
                if ($save) {
                    $moradoresArray = json_decode($form->moradores);
                    array_push($moradoresArray, $moradores);
                    $data = json_encode($moradoresArray);
                    $form->moradores = $data;
                    $moradores = [];
                }
                return;
            }

            if ($qId == 42) {
                $data = json_decode($form->conhecimentos_em_informatica);
                array_push($data, $answer->value);
                $form->conhecimentos_em_informatica = json_encode($data);
                return;
            }

            if (in_array($qId, [44, 45, 46])) {
                $save = false;
                switch ($qId) {
                    case 44:
                        $key = 'Nome do curso';
                        break;
                    case 45:
                        $key = 'Instituição do curso';
                        break;
                    case 46:
                        $key = 'Duração do curso';
                        $save = true;
                        break;
                }
                $cursos_complementares[$key] = $answer->value;
                if ($save) {
                    $cursosArray = json_decode($form->cursos_complementares);
                    array_push($cursosArray, $cursos_complementares);
                    $data = json_encode($cursosArray);
                    $form->cursos_complementares = $data;
                    $cursos_complementares = [];
                }
                return;
            }

            if (in_array($qId, [48, 49, 50])) {
                $save = false;
                switch ($qId) {
                    case 48:
                        $key = 'Empresa';
                        break;
                    case 49:
                        $key = 'Cargo';
                        break;
                    case 50:
                        $key = 'Período';
                        $save = true;
                        break;
                }
                $experiencia_profissional[$key] = $answer->value;
                if ($save) {
                    $experienciaArray = json_decode($form->experiencia_profissional);
                    array_push($experienciaArray, $experiencia_profissional);
                    $data = json_encode($experienciaArray);
                    $form->experiencia_profissional = $data;
                    $experiencia_profissional = [];
                }
                return;
            }

            if ($qId == 58) {
                $data = json_decode($form->no_espelho_voce_enxerga);
                array_push($data, $answer->value);
                $form->no_espelho_voce_enxerga = json_encode($data);
                return;
            }

            $form[Self::QUESTIONS_TO_FIELDS_MAP[$qId]] = $answer->value;
        });

        $form->setCreatedAt($entry->created_at);
        $form->save();
    }
}
