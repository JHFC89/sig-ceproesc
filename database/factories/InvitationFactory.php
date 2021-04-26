<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvitationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invitation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email'             => $this->faker->unique()->safeEmail,
            'code'              => 'TESTCODE1234',
            'registration_id'   => Registration::factory()->create(),
        ];
    }
}
