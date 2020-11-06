<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonRegisterController extends Controller
{
    public function create(Lesson $lesson)
    {
        abort_if($lesson->isRegistered(), 404);

        return view('lessons.register.create', compact('lesson'));
    }

    public function store(Lesson $lesson)
    {
        if($lesson->isRegistered()) {
            return response()->json(['error' => 'Lesson already registered'], 422);
        } 

        if(! $lesson->isForToday()) {
            return response()->json(['error' => 'Lesson is not available to register at this date'], 422);
        }

        request()->validate([
            'register' => 'required',
        ]);

        $lesson->register = request()->register;
        $lesson->save();

        return response('', 201);
    }
}
