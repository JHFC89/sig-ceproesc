<?php

namespace Database\Factories;

use App\Models\{Role, Registration, Company};
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

    public function forEmployer(int $company_id = 0)
    {
        if ($company_id === 0) {
            $company_id = Company::factory()->create()->id;
        }

        return $this->state(function (array $attributes) use ($company_id) {
            return [
                'rg'            => $this->faker->randomNumber(8),
                'role_id'       => Role::factory()->create(['name' => 'employer']),
                'company_id'    => $company_id,
            ];
        });
    }

    public function forAdmin()
    {
        return $this->state(function (array $attributes) {
           return [
               'role_id' => Role::factory()->create([
                   'name' => Role::ADMIN,
               ]),
            ];
        });
    }

    public function forInstructor()
    {
        return $this->state(function (array $attributes) {
           return [
                'birthdate' => now()->subYears(30)->format('Y-m-d'),
                'rg'        => $this->faker->randomNumber(8),
                'cpf'       => $this->fakeCpf(),
                'ctps'      => $this->faker->randomNumber(7),
                'role_id'   => Role::factory()->create(['name' => 'instructor']),
            ];
        });
    }

    public function forCoordinator()
    {
        return $this->state(function (array $attributes) {
           return [
               'role_id' => Role::factory()->create([
                   'name' => Role::COORDINATOR,
               ]),
            ];
        });
    }

    public function forNovice(int $company_id = 0)
    {
        if ($company_id === 0) {
            $company_id = Company::factory()->create()->id;
        }

        return $this->state(function (array $attributes) use ($company_id) {
           return [
                'name'              => $this->faker->name,
                'birthdate'         => now()->subYears(30)->format('Y-m-d'),
                'rg'                => $this->faker->randomNumber(8),
                'cpf'               => $this->fakeCpf(),
                'ctps'              => $this->faker->randomNumber(7),
                'responsable_name'  => $this->faker->name,
                'responsable_cpf'   => $this->fakeCpf(),
               'role_id'        => Role::factory()->create([
                   'name' => Role::NOVICE,
               ]),
               'employer_id'    => $company_id,
            ];
        });
    }

    private function fakeCpf()
    {
        $cpfA = $this->faker->randomNumber(3);
        $cpfB = $this->faker->randomNumber(3);
        $cpfC = $this->faker->randomNumber(3);
        $cpfD = $this->faker->randomNumber(2);

        return "{$cpfA}.{$cpfB}.{$cpfC}-{$cpfD}";
    }

}
