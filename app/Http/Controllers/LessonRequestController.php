<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\RectifyLessonRequest;
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
        if ($lesson->isRegistered()) {
            abort_if(request()->user()->cannot('createForLesson', [RectifyLessonRequest::class, $lesson]), 401);
            $requestType = 'rectification';

        } else {
            abort_if(request()->user()->cannot('createForLesson', [RegisterLessonRequest::class, $lesson]), 401);
            $requestType = 'expiration';
        }

        return view('lessons.requests.create', compact('lesson', 'requestType'));
    }

    public function store(Lesson $lesson)
    {
        if ($lesson->isRegistered()) {
            abort_if(request()->user()->cannot('storeForLesson', [RectifyLessonRequest::class, $lesson]), 401);
        } else {
            abort_if(request()->user()->cannot('storeForLesson', [RegisterLessonRequest::class, $lesson]), 401);
        }

        $validatedData = request()->validate([
            'justification' => 'required|string',
        ]);

        if ($lesson->isRegistered()) {
            $request = RectifyLessonRequest::for($lesson, $validatedData['justification']);
        } else {
            $request = RegisterLessonRequest::for($lesson, $validatedData['justification']);
        }

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
