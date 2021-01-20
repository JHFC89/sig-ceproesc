<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class EvaluationGradeController extends Controller
{
    public function store(Evaluation $evaluation)
    {
        abort_if(request()->user()->cannot('storeGrade', $evaluation), 401);

        $data = request()->validate([
            'gradesList' => 'required|array',
            'gradesList.*' => 'required|string',
        ]);

        $evaluation->record($data['gradesList']);

        session()->flash('status', 'notas registradas com sucesso!');

        return view('evaluations.show', compact('evaluation'));
    }
}
