<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\RegisterLessonRequest;

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

    public function update(RegisterLessonRequest $request)
    {
        abort_if(request()->user()->cannot('update', $request), 401);

        $request->release();

        session()->flash('status', 'Aula liberada para registro com sucesso!');

        return view('requests.show', compact('request'));
    }
}
