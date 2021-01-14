<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function create(Lesson $lesson)
    {
        abort_if(auth()->user()->cannot('createForLesson', [Evaluation::class, $lesson]), 401);

        return view('evaluations.create', compact('lesson'));
    }

    public function store(Lesson $lesson)
    {
        abort_if(auth()->user()->cannot('storeForLesson', [Evaluation::class, $lesson]), 401);

        $data = request()->validate([
            'label' => 'required|string',
            'description' => 'required|string',
        ]);

        $lesson->evaluation()->create($data);

        return view('evaluations.show');
    }
}
