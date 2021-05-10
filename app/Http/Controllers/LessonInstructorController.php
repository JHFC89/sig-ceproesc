<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\User;

class LessonInstructorController extends Controller
{
    public function update(Lesson $lesson)
    {
        abort_if(request()->user()->cannot('update', $lesson), 401);

        $data = request()->validate([
            'instructor' => [
                'required',
                'integer',
                'exists:App\Models\User,id',
            ],
        ]);

        $instructor = User::find($data['instructor']);

        abort_unless($lesson->discipline->isAttached($instructor), 404);

        $lesson->instructor()->associate($instructor->id)->save();

        session()->flash('status', 'Instrutor da aula alterado com sucesso!');

        return redirect()->route('lessons.edit', ['lesson' => $lesson]);
    }
}
