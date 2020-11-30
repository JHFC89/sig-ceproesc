<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Exceptions\NoviceNotEnrolledException;

class LessonDraftController extends Controller
{
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
            return response()->json(['error' => 'Lesson is not available to draft at this date'], 422);
        }

        request()->validate([
            'register' => 'required',
            'presenceList' => 'required',
            'presenceList.*' => 'boolean',
        ]);

        $lesson->register = request()->register;
        $lesson->save();

        try {
            collect(request()->presenceList)->each(function ($present, $id) use ($lesson) {
                if ($present) {
                    $lesson->registerPresence(User::find($id))->present();
                } else {
                    $lesson->registerPresence(User::find($id))->absent();
                }
            });
        } catch (NoviceNotEnrolledException $exception) {
            abort(403, $exception->getMessage());
        }

        return response('', 201);
    }
}
