<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreCompanyTest extends TestCase
{
    use RefreshDatabase;

    protected $data;

    protected $coordinator;

    protected function setUp():void
    {
        parent::setUp();

        $address = [
                'street'    => 'Test Street',
                'number'    => '123',
                'district'  => 'Test Garden',
                'city'      => 'Test City',
                'state'     => 'Test State',
                'country'   => 'Test Country',
                'cep'       => '12.123-123',
            ];

        $this->data = [
            'name'      => 'Test Ldta',
            'cnpj'      => '12.123.123/0001-12',
            'phone'     => '1234567',
            'address'   => $address,
        ];

        $this->coordinator = User::fakeCoordinator();
    }

    /** @test */
    public function coordinator_can_store_a_company()
    {
        $companies = Company::count();
        $data = $this->data;
        $address = $data['address'];

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $data));

        $response->assertOk()
                 ->assertViewHas('company')
                 ->assertViewIs('companies.show')
                 ->assertSessionHas('status', 'Empresa cadastrada com sucesso!');
        $this->assertCount($companies + 1, Company::all());
        $company = Company::where('cnpj', $data['cnpj'])->first();
        $this->assertEquals($data['name'], $company->name);
        $this->assertEquals($data['cnpj'], $company->cnpj);
        $this->assertEquals($data['phone'], $company->phones[0]->number);
        $this->assertEquals($address['street'], $company->address->street);
        $this->assertEquals($address['number'], $company->address->number);
        $this->assertEquals($address['district'], $company->address->district);
        $this->assertEquals($address['city'], $company->address->city);
        $this->assertEquals($address['state'], $company->address->state);
        $this->assertEquals($address['country'], $company->address->country);
        $this->assertEquals($address['cep'], $company->address->cep);
    }

    /** @test */
    public function guest_cannot_store_a_company()
    {
        $response = $this->post(route('companies.store', $this->data));

        $response->assertRedirect('login');
    }

    /** @test */
    public function user_without_role_cannot_store_a_company()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->post(route('companies.store', $this->data));

        $response->assertUnauthorized();
    }

    /** @test */
    public function instructor_cannot_store_a_company()
    {
        $instructor = User::fakeInstructor();

        $response = $this->actingAs($instructor)
                         ->post(route('companies.store', $this->data));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_store_a_company()
    {
        $novice = User::fakeNovice();

        $response = $this->actingAs($novice)
                         ->post(route('companies.store', $this->data));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_store_a_company()
    {
        $employer = User::fakeEmployer();

        $response = $this->actingAs($employer)
                         ->post(route('companies.store', $this->data));

        $response->assertUnauthorized();
    }

    /** @test */
    public function company_name_is_required()
    {
        $data = $this->data;
        unset($data['name']);

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $data));

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function company_name_must_be_unique()
    {
        $this->data['name'] = 'Unique Test Name';
        Company::factory()->create(['name' => 'Unique Test Name']);

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $this->data));

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function company_cnpj_is_required()
    {
        $data = $this->data;
        unset($data['cnpj']);

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $data));

        $response->assertSessionHasErrors('cnpj');
    }

    /** @test */
    public function company_cnpj_must_be_unique()
    {
        Company::factory()->create(['cnpj' => $this->data['cnpj']]);

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $this->data));

        $response->assertSessionHasErrors('cnpj');
    }

    /** @test */
    public function company_cnpj_must_not_have_less_than_18_characters()
    {
        $this->data['cnpj'] = '12.123.123/0001-1';

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $this->data));

        $response->assertSessionHasErrors('cnpj');
    }

    /** @test */
    public function company_cnpj_must_not_have_more_than_18_characters()
    {
        $this->data['cnpj'] = '12.123.123/0001-123';

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $this->data));

        $response->assertSessionHasErrors('cnpj');
    }

    /** @test */
    public function company_phone_is_required()
    {
        $data = $this->data;
        unset($data['phone']);

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $data));

        $response->assertSessionHasErrors('phone');
    }

    /** @test */
    public function company_address_is_required()
    {
        $data = $this->data;
        unset($data['address']);

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $data));

        $response->assertSessionHasErrors('address');
    }

    /** @test */
    public function company_street_is_required()
    {
        $data = $this->data;
        unset($data['address']['street']);

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $data));

        $response->assertSessionHasErrors('address.street');
    }

    /** @test */
    public function company_number_is_required()
    {
        $data = $this->data;
        unset($data['address']['number']);

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $data));

        $response->assertSessionHasErrors('address.number');
    }

    /** @test */
    public function company_district_is_required()
    {
        $data = $this->data;
        unset($data['address']['district']);

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $data));

        $response->assertSessionHasErrors('address.district');
    }

    /** @test */
    public function company_city_is_required()
    {
        $data = $this->data;
        unset($data['address']['city']);

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $data));

        $response->assertSessionHasErrors('address.city');
    }

    /** @test */
    public function company_state_is_required()
    {
        $data = $this->data;
        unset($data['address']['state']);

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $data));

        $response->assertSessionHasErrors('address.state');
    }

    /** @test */
    public function company_country_is_required()
    {
        $data = $this->data;
        unset($data['address']['country']);

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $data));

        $response->assertSessionHasErrors('address.country');
    }

    /** @test */
    public function company_cep_is_required()
    {
        $data = $this->data;
        unset($data['address']['cep']);

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $data));

        $response->assertSessionHasErrors('address.cep');
    }

    /** @test */
    public function company_cep_must_not_have_less_than_10_characters()
    {
        $this->data['address']['cep'] = '12.123-12';

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $this->data));

        $response->assertSessionHasErrors('address.cep');
    }

    /** @test */
    public function company_cep_must_not_have_more_than_10_characters()
    {
        $this->data['address']['cep'] = '12.123-1234';

        $response = $this->actingAs($this->coordinator)
                         ->post(route('companies.store', $this->data));

        $response->assertSessionHasErrors('address.cep');
    }
}
