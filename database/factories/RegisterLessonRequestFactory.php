<?php

namespace Database\Factories;

use App\Models\RegisterLessonRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegisterLessonRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RegisterLessonRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'justification' => 'Fake justification',
        ];
    }
}
