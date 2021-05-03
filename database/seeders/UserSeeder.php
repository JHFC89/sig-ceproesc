<?php

namespace Database\Seeders;

use App\Facades\InvitationCode;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\Registration;
use App\Models\Role;
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
        Role::factory()->create(['name' => 'admin']);

        // admin
        $registration = Registration::factory()->forAdmin()->create();
        $invitation = $registration->invitation()->save(new Invitation([
            'email' => 'admin@sig.com.br',
            'code' => InvitationCode::generate(),
        ]));
        $invitation->createUserFromArray(['password' => 'asdf']);

        // coordinator
        $registration = Registration::factory()->forCoordinator()->create();
        $invitation = $registration->invitation()->save(new Invitation([
            'email' => 'coordenador@sig.com.br',
            'code' => InvitationCode::generate(),
        ]));
        $invitation->createUserFromArray(['password' => 'asdf']);

        // instructor
        $registration = Registration::factory()->forInstructor()->create();
        $registration->phones()->create($this->fakePhone());
        $registration->address()->create($this->fakeAddress());
        $invitation = $registration->invitation()->save(new Invitation([
            'email' => 'instrutor@sig.com.br',
            'code' => InvitationCode::generate(),
        ]));
        $invitation->createUserFromArray(['password' => 'asdf']);


        // novice 1
        $registration = Registration::factory()->forNovice()->create();
        $registration->phones()->create($this->fakePhone());
        $registration->address()->create($this->fakeAddress());
        $invitation = $registration->invitation()->save(new Invitation([
            'email' => 'aprendiz@sig.com.br',
            'code' => InvitationCode::generate(),
        ]));
        $noviceA = $invitation->createUserFromArray(['password' => 'asdf']);

        // novice 2
        $registration = Registration::factory()->forNovice()->create();
        $registration->phones()->create($this->fakePhone());
        $registration->address()->create($this->fakeAddress());
        $invitation = $registration->invitation()->save(new Invitation([
            'email' => 'aprendiz2@sig.com.br',
            'code' => InvitationCode::generate(),
        ]));
        $noviceB = $invitation->createUserFromArray(['password' => 'asdf']);

        // employer
        $company = Company::factory()->create();
        $registration = Registration::factory()->forEmployer($company->id)->create();
        $registration->phones()->create($this->fakePhone());
        $registration->address()->create($this->fakeAddress());
        $invitation = $registration->invitation()->save(new Invitation([
            'email' => 'representante@sig.com.br',
            'code' => InvitationCode::generate(),
        ]));
        $employer = $invitation->createUserFromArray(['password' => 'asdf']);
        $employer->company->novices()->saveMany([
            $noviceA->registration,
            $noviceB->registration,  
        ]);
    }

    private function fakePhone()
    {
        return ['number' => '16 97897 9877'];
    }

    private function fakeAddress()
    {
        return [
            'street'    => 'Fake Street',
            'number'    => '123',
            'district'  => 'Fake Garden',
            'city'      => 'Fake City',
            'state'     => 'Fake State',
            'country'   => 'Fake Country',
            'cep'       => '12.123-123',
        ];
    }
}
