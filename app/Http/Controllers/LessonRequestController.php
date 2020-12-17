<?php

namespace App\Http\Controllers;

use App\Models\RegisterLessonRequest;
use App\Models\Lesson;

class LessonRequestController extends Controller
{
    public function create(Lesson $lesson)
    {
        abort_unless(request()->user()->isInstructor(), 404);

        abort_unless($lesson->isForInstructor(request()->user()), 401);

        abort_unless($lesson->isExpired(), 404);

        abort_if($lesson->hasOpenRequest(), 404);

        return view('lessons.requests.create', compact('lesson'));
    }

    public function store(Lesson $lesson)
    {
        abort_unless(request()->user()->isInstructor(), 401);

        abort_unless($lesson->isForInstructor(request()->user()), 401);

        abort_if($lesson->hasOpenRequest(), 401);

        $validatedData = request()->validate([
            'justification' => 'required|string',
        ]);

        RegisterLessonRequest::for($lesson, $validatedData['justification']);

        return view('lessons.requests.show');
    }
}
