<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegistrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Registration::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'      => $this->faker->name,
            'role_id'   => Role::factory()->create(),
        ];
    }

    public function forEmployer(int $company_id)
    {
        return $this->state(function (array $attributes) use ($company_id) {
            return [
                'rg'            => $this->faker->randomNumber(8),
                'role_id'       => Role::factory()->create(['name' => 'employer']),
                'company_id'    => $company_id,
            ];
        });
    }
}
