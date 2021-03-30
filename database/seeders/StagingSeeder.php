<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StagingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $coordinator = User::factory()
            ->hasRoles(1, ['name' => 'coordinator'])
            ->create([
                'name' => 'João Henrique',
                'email' => 'joao@sig.com.br',
                'password' => Hash::make('joao'),
            ]);

        $coordinator = User::factory()
            ->hasRoles(1, ['name' => 'coordinator'])
            ->create([
                'name' => 'Beatriz Salles',
                'email' => 'beatriz@sig.com.br',
                'password' => Hash::make('beatriz'),
            ]);

        $coordinator = User::factory()
            ->hasRoles(1, ['name' => 'coordinator'])
            ->create([
                'name' => 'Carolina Ferreira',
                'email' => 'carolina@sig.com.br',
                'password' => Hash::make('carolina'),
            ]);

        $coordinator = User::factory()
            ->hasRoles(1, ['name' => 'coordinator'])
            ->create([
                'name' => 'Gizela Gomides',
                'email' => 'gizela@sig.com.br',
                'password' => Hash::make('gizela'),
            ]);

        $coordinator = User::factory()
            ->hasRoles(1, ['name' => 'coordinator'])
            ->create([
                'name' => 'Carlos',
                'email' => 'carlos@sig.com.br',
                'password' => Hash::make('carlos'),
            ]);
    }
}
