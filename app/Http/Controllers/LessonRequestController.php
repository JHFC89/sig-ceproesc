<?php

namespace App\Http\Controllers;

use App\Models\RegisterLessonRequest;
use App\Models\Lesson;

class LessonRequestController extends Controller
{
    public function show(RegisterLessonRequest $request)
    {
        abort_if(request()->user()->cannot('view', $request), 401);

        return view('requests.show', compact('request'));
    }

    public function create(Lesson $lesson)
    {
        abort_if(request()->user()->cannot('createForLesson', [RegisterLessonRequest::class, $lesson]), 401);

        return view('lessons.requests.create', compact('lesson'));
    }

    public function store(Lesson $lesson)
    {
        abort_if(request()->user()->cannot('storeForLesson', [RegisterLessonRequest::class, $lesson]), 401);

        $validatedData = request()->validate([
            'justification' => 'required|string',
        ]);

        $request = RegisterLessonRequest::for($lesson, $validatedData['justification']);

        return view('requests.show', compact('request'));
    }
}
