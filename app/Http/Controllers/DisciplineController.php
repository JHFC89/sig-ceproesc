<?php

namespace App\Http\Controllers;

use App\Models\Discipline;
use Illuminate\Http\Request;

class DisciplineController extends Controller
{
    public function show(Discipline $discipline)
    {
        abort_if(request()->user()->cannot('view', $discipline), 401);

        return view('disciplines.show', compact('discipline'));
    }
}
