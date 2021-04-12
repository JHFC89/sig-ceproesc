<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_check_is_employer()
    {
        $employer = User::factory()->hasRoles(1, ['name' => 'employer'])
                                   ->create();

        $this->assertTrue($employer->isEmployer());
    }

    /** @test */
    public function can_check_is_employer_of_a_specific_novice()
    {
        $employer = User::fakeEmployer();
        $noviceForEmployer = User::fakeNovice();
        $noviceNotForEmployer = User::fakeNovice();
        $company = Company::factory()->create();
        $company->employers()->save($employer);
        $company->novices()->save($noviceForEmployer);

        $noviceForEmployerResult = $employer->isEmployerOf($noviceForEmployer);
        $noviceNotForEmployerResult = $employer->isEmployerOf($noviceNotForEmployer);
        
        $this->assertTrue($noviceForEmployerResult);
        $this->assertFalse($noviceNotForEmployerResult);
    }

    /** @test */
    public function can_get_all_novices()
    {
        $novices = User::factory()->count(3)
                                    ->hasRoles(1, ['name' => 'novices'])
                                    ->create();
        $employer = User::fakeEmployer();
        $company = Company::factory()->create();
        $company->employers()->save($employer);
        $company->novices()->saveMany([
            $novices[0],
            $novices[1],
            $novices[2],
        ]);

        $result = $employer->refresh()->novices;

        $this->assertEquals(3, $result->count());
    }

    /** @test */
    public function two_employers_from_the_same_company_get_the_same_novices() 
    {
        $employerA = User::fakeEmployer();
        $employerB = User::fakeEmployer();
        $company = Company::factory()->create();
        $company->employers()->saveMany([$employerA, $employerB]);
        $novice = User::fakeNovice();
        $company->novices()->save($novice);

        $resultA = $employerA->isEmployerOf($novice);
        $resultB = $employerB->isEmployerOf($novice);

        $this->assertTrue($resultA);
        $this->assertTrue($resultB);
    }
}
