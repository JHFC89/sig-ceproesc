<?php

namespace Database\Seeders;

use App\Models\User;
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
        User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create([
                'email' => 'instrutor@sig.com.br',
                'password' => Hash::make('senha123'),
            ]);
    }
}
