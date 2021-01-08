<?php

namespace Database\Factories;

use App\Models\LessonRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LessonRequest::class;

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
