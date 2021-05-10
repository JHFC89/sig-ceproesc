<?php

namespace App\Http\Controllers;

use App\Models\CourseClass;
use App\Models\Lesson;

class LessonController extends Controller
{
    public function index(CourseClass $courseClass)
    {
        $user = request()->user();

        abort_if($user->cannot('viewAny', [Lesson::class, $courseClass]), 401);

        if ($user->isInstructor()) {
            $lessons = $courseClass->lessons()
                                   ->where('instructor_id', $user->id)
                                   ->oldest('date')
                                   ->paginate();
        } else {
            $lessons = $courseClass->lessons()->oldest('date')->paginate();
        }

        $lessons->load(['instructor', 'discipline']);

        return view('lessons.index', compact('lessons'));
    }

    public function show(Lesson $lesson)
    {
        abort_if(request()->user()->cannot('view', $lesson), 404);

        return view('lessons.show', compact('lesson'));
    }

    public function create(CourseClass $courseClass)
    {
        abort_if(request()->user()->cannot('create', Lesson::class), 401);

        if ($courseClass->hasLessons()) {
            return redirect()->route('classes.show', [
                'courseClass' => $courseClass
            ])->with('status', 'Aulas já cadastradas!');
        }

        $courseClass->load(
            'course.disciplines', 
            'course.disciplines.instructors'
        );

        return view('classes.lessons.create', compact('courseClass'));
    }

    public function edit(Lesson $lesson)
    {
        abort_if(request()->user()->cannot('update', $lesson), 401);

        $lesson->load('discipline.instructors', 'instructor');

        return view('lessons.edit', compact('lesson'));
    }
}
