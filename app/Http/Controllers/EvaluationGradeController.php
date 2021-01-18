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

        request()->validate([
            'gradesList' => 'required|array',
            'gradesList.*' => 'required|string',
        ]);

        collect(request()->gradesList)->each(function ($grade, $novice_id) use ($evaluation) {
            $evaluation->recordGradeForNovice(User::find($novice_id), $grade);
        });
    }
}
