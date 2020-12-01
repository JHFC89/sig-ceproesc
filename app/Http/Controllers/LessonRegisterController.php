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
            'presenceList.*.presence' => 'required|boolean',
            'presenceList.*.observation' => 'sometimes|nullable|string',
        ]);

        $lesson->register = request()->register;

        try {
            collect(request()->presenceList)->each(function ($novice, $id) use ($lesson) {
                if ($novice['presence']) {
                    $lesson->registerFor(User::find($id))->present();
                } else {
                    $lesson->registerFor(User::find($id))->absent();
                }

                if (isset($novice['observation'])) {
                    $lesson->observation($novice['observation']);
                }

                $lesson->complete();
            });

        } catch (NoviceNotEnrolledException $exception) {
            abort(403, $exception->getMessage());
        }

        $lesson->registered_at = now();
        $lesson->save();

        return response('', 201);
    }
}
