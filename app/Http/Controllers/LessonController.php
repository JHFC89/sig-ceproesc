<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function show(Lesson $lesson)
    {
        abort_if(auth()->user()->hasNoRole(), 404);

        if (auth()->user()->isInstructor()) {
            abort_unless($lesson->isForInstructor(auth()->user()), 404);
        }

        if (auth()->user()->isNovice()) {
            abort_unless($lesson->isEnrolled(auth()->user()), 404);
        }

        if (auth()->user()->isEmployer()) {
            abort_unless($lesson->hasNovicesForEmployer(auth()->user()), 404);
        }

        return view('lessons.show', compact('lesson'));
    }
}
