<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonRegisterController extends Controller
{
    public function create(Request $request, Lesson $lesson)
    {
        abort_unless($request->user()->isInstructor(), 401);

        abort_if($lesson->isRegistered(), 404);

        abort_unless($lesson->isForToday(), 404);

        abort_unless($lesson->isForInstructor($request->user()), 401);

        return view('lessons.register.create', compact('lesson'));
    }

    public function store(Lesson $lesson)
    {
        if(! request()->user()->isInstructor()) {
            return response()->json(['error' => 'Action not authorized for this user'], 401);
        } 

        if(! $lesson->isForInstructor(request()->user())) {
            return response()->json(['error' => 'Action not authorized for this instructor'], 401);
        } 

        if($lesson->isRegistered()) {
            return response()->json(['error' => 'Lesson already registered'], 422);
        } 

        if(! $lesson->isForToday()) {
            return response()->json(['error' => 'Lesson is not available to register at this date'], 422);
        }

        request()->validate([
            'register' => 'required',
            'presenceList' => 'required',
        ]);

        $lesson->register = request()->register;
        $lesson->registered_at = now();
        $lesson->save();

        try {
            $presence = collect(request()->presenceList);
            $presence->each(function ($frequency, $id) use ($lesson) {
                $lesson->registerPresence(User::find($id), $frequency);
            });
        } catch (NoviceNotEnrolledException $exception) {
            abort(403, $exception->getMessage());
        }

        return response('', 201);
    }
}
