<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Exceptions\NoviceNotEnrolledException;

class LessonDraftController extends Controller
{
    public function store(Lesson $lesson)
    {
        $response = Gate::inspect('storeDraft', $lesson);

        if ($response->denied()) {
            return response()->json(['error' => $response->message()], $response->code());
        }

        request()->validate([
            'register' => 'required',
            'presenceList' => 'required',
            'presenceList.*.presence' => 'required|boolean',
            'presenceList.*.observation' => 'sometimes|nullable|string',
        ]);

        $lesson->register = request()->register;
        $lesson->save();

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

        return response('', 201);
    }
}
