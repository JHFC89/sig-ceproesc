<?php

namespace Database\Factories;

use App\Models\Discipline;
use Illuminate\Database\Eloquent\Factories\Factory;

class DisciplineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Discipline::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'Fake Discipline',
            'basic' => true,
            'duration' => 30,
        ];
    }

    public function basic()
    {
        return $this->state(function (array $attributes) {
            return [
                'basic' => true,
            ];
        });
    }
}
