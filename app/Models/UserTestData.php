<?php

namespace App\Models;

trait UserTestData
{
    public static function fakeAdmin()
    {
        return static::factory()->hasRoles(['name' => Role::ADMIN])
                                ->create();
    }

    public static function fakeCoordinator()
    {
        return static::factory()->hasRoles(['name' => Role::COORDINATOR])
                                ->create();
    }

    public static function fakeInstructor()
    {
        return static::factory()->hasRoles(['name' => Role::INSTRUCTOR])
                                ->create();
    }

    public static function fakeNovice()
    {
        return static::factory()->hasRoles(['name' => Role::NOVICE])
                                ->create();
    }

    public static function fakeEmployer()
    {
        $employer = static::factory()->create();

        $employer->registration->user()->dissociate()->save();

        $registration = Registration::factory()->create([
            'role_id'       => Role::factory()->create([
                'name' => Role::EMPLOYER
            ]),
            'company_id'    => Company::factory()->create(),
        ]);

        return $registration->attachUser($employer);
    }
}
