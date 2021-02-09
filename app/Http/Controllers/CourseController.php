<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Discipline;

class CourseController extends Controller
{
    public function show(Course $course)
    {
        abort_if(request()->user()->cannot('view', $course), 401);

        return view('courses.show', compact('course'));
    }

    public function store()
    {
        abort_if(request()->user()->cannot('create', Course::class), 401);

        request()->validate([
            'name'          => 'required|string|unique:courses,name',
            'duration'      => [
                'required',
                'integer',
            ],
            'disciplines'   => [
                'bail',
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    if (! $this->validateDisciplinesDuration($value)) {
                        $fail('The disciplines duration is invalid.');
                    }
                },
            ]
        ]);

        $course = Course::create(request()->only('name', 'duration'));

        $course->addDisciplines(request()->disciplines);

        return view('courses.show', compact('course'));
    }

    protected function validateDisciplinesDuration(array $disciplines)
    {
        $disciplinesTotalDuration = Discipline::durationWhereIn($disciplines);

        return $disciplinesTotalDuration === request()->duration;
    }
}
