<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CourseClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // instructor
        User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create([
                'name' => 'Instrutor 1',
                'email' => 'instrutor@sig.com.br',
                'password' => Hash::make('asdf'),
            ]);

        // instructor 2
        User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create([
                'name' => 'Instrutor 2',
                'email' => 'instrutor2@sig.com.br',
                'password' => Hash::make('asdf'),
            ]);

        // instructor without classes
        User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create([
                'name' => 'Instrutor Sem Aulas',
                'email' => 'instrutorsemaula@sig.com.br',
                'password' => Hash::make('asdf'),
            ]);

        // novice
        $noviceA = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create([
                'name' => 'Aprendiz 1',
                'email' => 'aprendiz@sig.com.br',
                'password' => Hash::make('asdf'),
            ]);

        CourseClass::where('name', 'janeiro - 2020')->first()->subscribe($noviceA);

        // novice 2
        $noviceB = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create([
                'name' => 'Aprendiz 2',
                'email' => 'aprendiz2@sig.com.br',
                'password' => Hash::make('asdf'),
            ]);

        CourseClass::where('name', 'julho - 2020')->first()->subscribe($noviceB);

        // novice 3
        $noviceC = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create([
                'name' => 'Aprendiz 3',
                'email' => 'aprendiz3@sig.com.br',
                'password' => Hash::make('asdf'),
            ]);

        CourseClass::where('name', 'janeiro - 2021')->first()->subscribe($noviceC);

        // employer
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create([
                'name' => 'Empresa 1',
                'email' => 'empresa@sig.com.br',
                'password' => Hash::make('asdf'),
            ]);

        $employer->novices()->saveMany([$noviceA, $noviceB]);
    }
}
