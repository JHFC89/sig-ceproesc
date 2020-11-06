<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lesson::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'instructor'    => 'John Doe',
            'date'          => Carbon::parse('+2 weeks'),
            'class'         => '2021 - janeiro',
            'discipline'    => 'fake discipline',
            'hourly_load'   => '123hr',
            'novice'        => 'Mary Jane'
        ];
    }

    public function registered()
    {
        return $this->state(function (array $attributes) {
            return [
                'register' => 'Fake register.',
                'registered_at' => Carbon::parse('+2 weeks'),
            ];
        });
    }

    public function notRegistered()
    {
        return $this->state(function (array $attributes) {
            return [
                'register' => null,
            ];
        });
    }

    public function forToday()
    {
        return $this->state(function (array $attributes) {
            return [
                'date' => Carbon::now(),
            ];
        });

    }

    public function notForToday()
    {
        return $this->state(function (array $attributes) {
            return [
                'date' => Carbon::parse('+2 weeks'),
            ];
        });
    }

    public function draft()
    {
        return $this->state(function (array $attributes) {
            return [
                'register' => 'Fake draft register.',
            ];
        });
    }
}
