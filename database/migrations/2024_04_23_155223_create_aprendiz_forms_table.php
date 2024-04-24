<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\AprendizForm;

class CreateAprendizFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aprendiz_forms', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nome', 255);
            $table->string('email', 255);
            $table->date('data_de_nascimento');
            $table->enum('genero', AprendizForm::GENEROS);
            $table->enum('habilidade_manual', AprendizForm::HABILIDADES_MANUAIS);
            $table->enum('estado_de_naturalidade', AprendizForm::ESTADOS);
            $table->string('cidade_onde_nasceu', 100);
            $table->string('cidade_onde_mora', 100);
            $table->string('logradouro', 255);
            $table->string('numero', 10);
            $table->string('cep', 10);
            $table->enum('zona', AprendizForm::ZONAS);
            $table->string('bairro', 255);
            $table->string('complemento', 255)->nullable();
            $table->string('telefone', 15);
            $table->string('telefone_de_recado', 15);
            $table->enum('carteira_de_habilitação', ['sim', 'não']);
            $table->enum('categoria', AprendizForm::CATEGORIAS_CNH);
            $table->string('facebook', 255);
            $table->string('instagram', 255);
            $table->unsignedTinyInteger('quantas_pessoas_moram_com_voce');
            $table->json('moradores')->nullable();
            $table->enum('a_familia_recebe_algum_auxilio_do_governo', ['sim', 'não']);
            $table->string('cpf', 14)->unique();
            $table->string('cpf_do_responsavel', 14);
            $table->string('nome_do_responsavel', 255);
            $table->enum('carteira_de_trabalho', AprendizForm::CARTEIRA_DE_TRABALHO);
            $table->string('numero_de_serie', 255);
            $table->string('rg', 50);
            $table->string('titulo_de_eleitor', 50)->nullable();
            $table->enum('alistamento_militar', AprendizForm::ALISTAMENTO_MILITAR)->nullable();
            $table->string('numero_de_reservista', 50)->nullable();
            $table->enum('escolaridade', AprendizForm::ESCOLARIDADE);
            $table->enum('situacao_escolaridade', AprendizForm::SITUACAO_ESCOLARIDADE);
            $table->string('instituicao_de_ensino', 255);
            $table->string('curso', 255)->nullable();
            $table->enum('nivel_de_conhecimentos_em_informatica', AprendizForm::NIVEL_INFORMATICA);
            $table->json('conhecimentos_em_informatica');
            $table->enum('possui_cursos_complementares', ['sim', 'não']);
            $table->json('cursos_complementares')->nullable();
            $table->enum('possui_experiencia_profissional', ['sim', 'não']);
            $table->json('experiencia_profissional')->nullable();
            $table->enum('esta_empregado', ['sim', 'não']);
            $table->string('quais_seus_principais_objetivos', 255);
            $table->string('expectativas_com_o_programa', 255);
            $table->enum('comportamento_que_se_identifica', AprendizForm::COMPORTAMENTOS_QUE_SE_IDENTIFICA);
            $table->string('uma_frase', 255);
            $table->string('uma_música', 255);
            $table->string('pode_faltar_tudo_menos', 255);
            $table->json('no_espelho_voce_enxerga');
            $table->string('acrescentaria_na_personalidade', 255);
            $table->string('qual_profissao_gostaria', 255);
            $table->string('mensagem_para_humanidade', 255);
            $table->enum('como_chegou_ao_ceproesc', AprendizForm::COMO_CHEGOU_CEPROESC);
            $table->string('historico', 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aprendiz_forms');
    }
}
