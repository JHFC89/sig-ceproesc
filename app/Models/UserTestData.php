<?php

namespace App\Models;

trait UserTestData
{
    public static function fakeCoordinator()
    {
        return static::factory()->hasRoles(['name' => 'coordinator'])->create();
    }

    public static function fakeInstructor()
    {
        return static::factory()->hasRoles(['name' => 'instructor'])->create();
    }

    public static function fakeNovice()
    {
        return static::factory()->hasRoles(['name' => 'novice'])->create();
    }

    public static function fakeEmployer()
    {
        return static::factory()->hasRoles(['name' => 'employer'])
                                ->forCompany()
                                ->create();
    }
}
