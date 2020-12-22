<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LessonRegisterController extends Controller
{
    public function create(Request $request, Lesson $lesson)
    {
        $response = Gate::inspect('createRegister', $lesson);

        abort_unless($response->allowed(), $response->code());

        return view('lessons.register.create', compact('lesson'));
    }

    public function store(Lesson $lesson)
    {
        $response = Gate::inspect('storeRegister', $lesson);

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
