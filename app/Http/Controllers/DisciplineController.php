<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Discipline;
use Illuminate\Http\Request;

class DisciplineController extends Controller
{
    public function show(Discipline $discipline)
    {
        abort_if(request()->user()->cannot('view', $discipline), 401);

        return view('disciplines.show', compact('discipline'));
    }

    public function store()
    {
        abort_if(request()->user()->cannot('create', Discipline::class), 401);

        request()->validate([
            'name'          => 'required|string',
            'basic'         => 'required|boolean',
            'duration'      => 'required|integer',
            'instructors'   => 'required|array',
        ]);

        $discipline = Discipline::create(
            request()->only('name', 'basic', 'duration')
        );

        $discipline->attachInstructors(request()->instructors);

        return view('disciplines.show', compact('discipline'));
    }
}
