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
