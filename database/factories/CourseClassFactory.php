<?php

namespace Database\Factories;

use App\Models\Course;
use Carbon\Carbon;
use App\Models\CourseClass;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseClassFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CourseClass::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $begin = Carbon::parse('+1 week');
        $end = $begin->copy()->addMonths(3);
        $intro_begin = $begin;
        $intro_end = $begin->copy()->addDays(10);
        $vacation_begin = $begin->copy()->addDays(30);
        $vacation_end = $begin->copy()->addDays(45);
        return [
            'name'                                  => 'test class name',
            'city'                                  => 'test city',
            'begin'                                 => $begin,
            'end'                                   => $end,
            'intro_begin'                           => $intro_begin,
            'intro_end'                             => $intro_end,
            'first_theoretical_activity_day'        => 'friday',
            'first_theoretical_activity_duration'   => 4,
            'second_theoretical_activity_day'       => 'saturday',
            'second_theoretical_activity_duration'  => 5,
            'practical_duration'                    => 300,
            'vacation_begin'                        => $vacation_begin,
            'vacation_end'                          => $vacation_end,
            'course_id'                             => Course::factory()->create(),
        ];
    }
}
