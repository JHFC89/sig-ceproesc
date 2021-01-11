<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonRequest;

class LessonRequestController extends Controller
{
    public function show(LessonRequest $request)
    {
        abort_if(request()->user()->cannot('view', $request), 401);

        return view('requests.show', compact('request'));
    }

    public function create(Lesson $lesson)
    {
        abort_if(request()->user()->cannot('createForLesson', [LessonRequest::class, $lesson]), 401);

        $requestType = LessonRequest::availableRequestTypeForLesson($lesson);

        return view('lessons.requests.create', compact('lesson', 'requestType'));
    }

    public function store(Lesson $lesson)
    {
        abort_if(request()->user()->cannot('storeForLesson', [LessonRequest::class, $lesson]), 401);

        $validatedData = request()->validate([
            'justification' => 'required|string',
        ]);

        $request = LessonRequest::for($lesson, $validatedData['justification']);

        return view('requests.show', compact('request'));
    }

    public function update(LessonRequest $request)
    {
        abort_if(request()->user()->cannot('update', $request), 401);

        $request->release();

        if ($request->isRectification()) {
            session()->flash('status', 'Aula liberada para retificação com sucesso!');
        } else {
            session()->flash('status', 'Aula liberada para registro com sucesso!');
        }

        return view('requests.show', compact('request'));
    }
}
