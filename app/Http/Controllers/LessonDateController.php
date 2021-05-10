<?php

namespace App\Http\Controllers;

use App\Models\Lesson;

class LessonDateController extends Controller
{
    public function update(Lesson $lesson)
    {
        abort_if(request()->user()->cannot('update', $lesson), 401);

        $data = request()->validate([
            'date'          => ['required'],
            'date.day'      => ['required', 'integer'],
            'date.month'    => ['required', 'integer'],
            'date.year'     => ['required', 'integer'],
        ]);

        $date = $data['date'];

        $lesson->date = "{$date['year']}-{$date['month']}-{$date['day']}";

        $lesson->save();

        session()->flash('status', 'Data da aula alterada com sucesso!');

        return redirect()->route('lessons.edit', ['lesson' => $lesson]);
    }
}
