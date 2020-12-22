<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function show(Lesson $lesson)
    {
        abort_if(request()->user()->cannot('view', $lesson), 404);

        return view('lessons.show', compact('lesson'));
    }
}
