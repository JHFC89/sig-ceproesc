<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonDraftController extends Controller
{
    public function store(Lesson $lesson)
    {
        if($lesson->isRegistered()) {
            return response()->json(['error' => 'Lesson already registered'], 422);
        } 

        if(! $lesson->isForToday()) {
            return response()->json(['error' => 'Lesson is not available to draft at this date'], 422);
        }

        request()->validate([
            'register' => 'required',
        ]);

        $lesson->register = request()->register;
        $lesson->save();

        return response('', 201);
    }
}
