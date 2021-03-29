<?php

namespace Database\Factories;

use App\Models\Discipline;
use Carbon\Carbon;
use App\Models\User;
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
            'instructor_id' => User::factory()->hasRoles(1, ['name' => 'instructor']),
            'date'          => Carbon::parse('+2 weeks'),
            'discipline_id' => Discipline::factory()->create(),
            'hourly_load'   => '123hr',
            'type'          => 'first',
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

    public function expired()
    {
        return $this->state(function (array $attributes) {
            return [
                'date' => Carbon::now()->subDay()->subSecond(),
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

    public function forYesterday()
    {
        return $this->state(function (array $attributes) {
            return [
                'date' => Carbon::parse('yesterday'),
            ];
        });
    }

    public function forTomorrow()
    {
        return $this->state(function (array $attributes) {
            return [
                'date' => Carbon::parse('tomorrow'),
            ];
        });
    }

    public function thisWeek()
    {
        return $this->state(function (array $attributes) {
            return [
                'date' => Carbon::now(),
            ];
        });
    }

    public function lastWeek()
    {
        return $this->state(function (array $attributes) {
            return [
                'date' => Carbon::parse('-1 week'),
            ];
        });
    }

    public function nextWeek()
    {
        return $this->state(function (array $attributes) {
            return [
                'date' => Carbon::parse('+1 week'),
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

    public function instructor(User $instructor)
    {
        return $this->state(function (array $attributes) use ($instructor) {
            return [
                'instructor_id' => $instructor,
            ];
        });
    }
}
