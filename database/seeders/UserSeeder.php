<?php

namespace Database\Seeders;

use App\Facades\InvitationCode;
use App\Models\Company;
use App\Models\Invitation;
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
                'email' => 'instrutor@sig.com.br',
                'password' => Hash::make('asdf'),
            ]);

        // instructor 2
        User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create([
                'email' => 'instrutor2@sig.com.br',
                'password' => Hash::make('asdf'),
            ]);

        // instructor without classes
        User::factory()
            ->hasRoles(1, ['name' => 'instructor'])
            ->create([
                'email' => 'instrutorsemaula@sig.com.br',
                'password' => Hash::make('asdf'),
            ]);

        // novice
        $noviceA = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create([
                'email' => 'aprendiz@sig.com.br',
                'password' => Hash::make('asdf'),
            ]);

        // novice 2
        $noviceB = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create([
                'email' => 'aprendiz2@sig.com.br',
                'password' => Hash::make('asdf'),
            ]);

        // novice 3
        $noviceC = User::factory()
            ->hasRoles(1, ['name' => 'novice'])
            ->create([
                'email' => 'aprendiz3@sig.com.br',
                'password' => Hash::make('asdf'),
            ]);

        // employer
        $employer = User::factory()
            ->hasRoles(1, ['name' => 'employer'])
            ->create([
                'email' => 'empresa@sig.com.br',
                'password' => Hash::make('asdf'),
            ]);

        $company = Company::factory()->create();
        $employer->registration()->update(['company_id' => $company->id]);
        $employer->registration->invitation()->save(new Invitation([
            'email' => $employer->email,
            'code'  => InvitationCode::generate(),
        ]));
        $employer->company->novices()->saveMany([
            $noviceA->registration,
            $noviceB->registration,  
        ]);

        // coordinator
        $coordinator = User::factory()
            ->hasRoles(1, ['name' => 'coordinator'])
            ->create([
                'email' => 'coordenador@sig.com.br',
                'password' => Hash::make('asdf'),
            ]);
    }
}
