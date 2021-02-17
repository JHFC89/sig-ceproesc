<?php

namespace Database\Factories;

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
        $vacation_begin = $begin->copy()->addDays(30);
        $vacation_end = $begin->copy()->addDays(45);
        return [
            'name'                                  => 'test class name',
            'begin'                                 => $begin,
            'end'                                   => $end,
            'first_theoretical_activity_day'        => 'friday',
            'first_theoretical_activity_duration'   => 4,
            'second_theoretical_activity_day'       => 'saturday',
            'second_theoretical_activity_duration'  => 5,
            'vacation_begin'                        => $vacation_begin,
            'vacation_end'                          => $vacation_end,
            'course_id'                             => 1,
        ];
    }
}
