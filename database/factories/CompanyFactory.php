<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $cnpjA = $this->faker->randomNumber(2);
        $cnpjB = $this->faker->randomNumber(3);
        $cnpjC = $this->faker->randomNumber(3);
        $cnpjD = $this->faker->randomNumber(2);
        $cnpj = "{$cnpjA}.{$cnpjB}.{$cnpjC}/0001-{$cnpjD}";

        return [
            'name' => $this->faker->company,
            'cnpj' => $cnpj,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Company $company) {
            $company->phones()->create(['number' => '123456789']);

            $company->address()->create([
                'street'    => 'Fake Street',
                'number'    => '123',
                'district'  => 'Fake Garden',
                'city'      => 'Fake City',
                'state'     => 'Fake State',
                'country'   => 'Fake Country',
                'cep'       => '12.123-123',
            ]);
        });
    }
}
