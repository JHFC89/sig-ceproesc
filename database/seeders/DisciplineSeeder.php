<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Discipline;
use Illuminate\Database\Seeder;

class DisciplineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $instructors = User::factory()->count(4)->create();
        $instructors->each(function ($instructor) {
            $instructor->turnIntoInstructor();
        });

        $this->disciplines()->each(function ($discipline) use ($instructors){
            $discipline = Discipline::factory()->create([
                'name'      => $discipline->name,
                'duration'  => $discipline->duration,
                'basic'     => $discipline->basic,
            ]);

            $discipline->attachInstructors($instructors->pluck('id'));
        });
    }

    private function disciplines()
    {
       return collect([
            (object) [
                'name'      => 'O Mundo do Trabalho',
                'duration'  => 30,
                'basic'     => true,
            ],
            (object) [
                'name'      => 'Comunicação',
                'duration'  => 30,
                'basic'     => true,
            ],
            (object) [
                'name'      => 'Cidadania Digital',
                'duration'  => 30,
                'basic'     => true,
            ],
            (object) [
                'name'      => 'Raciocínio Lógico Matemático',
                'duration'  => 30,
                'basic'     => true,
            ],
            (object) [
                'name'      => 'Educação Ambiental',
                'duration'  => 30,
                'basic'     => true,
            ],
            (object) [
                'name'      => 'Diversidade Cultural Brasileira',
                'duration'  => 30,
                'basic'     => true,
            ],
            (object) [
                'name'      => 'Direitos Humanos e Segurança Pública',
                'duration'  => 50,
                'basic'     => true,
            ],
            (object) [
                'name'      => 'Saúde e Qualidade de Vida',
                'duration'  => 40,
                'basic'     => true,
            ],
            (object) [
                'name'      => 'Organização Empresarial',
                'duration'  => 48,
                'basic'     => false,
            ],
            (object) [
                'name'      => 'Empreendedorismo',
                'duration'  => 48,
                'basic'     => false,
            ],
            (object) [
                'name'      => 'Tecnologia da Informação',
                'duration'  => 48,
                'basic'     => false,
            ],
            (object) [
                'name'      => 'Comunicação Empresarial',
                'duration'  => 48,
                'basic'     => false,
            ],
            (object) [
                'name'      => 'Relações de Trabalho',
                'duration'  => 48,
                'basic'     => false,
            ],
            (object) [
                'name'      => 'Saúde e Segurança do Trabalho',
                'duration'  => 42,
                'basic'     => false,
            ],
       ]);
    }
}
