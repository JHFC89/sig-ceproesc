<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Discipline;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DisciplineController extends Controller
{
    public function index()
    {
        abort_if(request()->user()->cannot('viewAny', Discipline::class), 401);

        $disciplines = Discipline::all();

        return view('disciplines.index', compact('disciplines'));
    }

    public function show(Discipline $discipline)
    {
        abort_if(request()->user()->cannot('view', $discipline), 401);

        return view('disciplines.show', compact('discipline'));
    }

    public function create()
    {
        abort_if(request()->user()->cannot('create', Discipline::class), 401);

        $instructors = User::whereInstructor()->get();

        return view('disciplines.create', compact('instructors'));
    }

    public function store()
    {
        abort_if(request()->user()->cannot('create', Discipline::class), 401);

        request()->validate([
            'name'          => 'required|string|unique:disciplines,name',
            'basic'         => 'required|boolean',
            'duration'      => 'required|integer',
            'instructors'   => 'required|array',
        ]);

        $discipline = Discipline::create(
            request()->only('name', 'basic', 'duration')
        );

        $discipline->attachInstructors(request()->instructors);

        session()->flash('status', 'Disciplina cadastrada com sucesso!');

        return view('disciplines.show', compact('discipline'));
    }

    public function edit(Discipline $discipline)
    {
        abort_if(request()->user()->cannot('update', $discipline), 401);

        $instructors = User::whereInstructor()->get();

        return view('disciplines.edit', compact('instructors', 'discipline'));
    }

    public function update(Discipline $discipline)
    {
        abort_if(request()->user()->cannot('update', $discipline), 401);

        request()->validate([
            'name'          => [
                'required',
                'string',
                Rule::unique('disciplines')->ignore($discipline),
            ],
            'basic'         => 'required|boolean',
            'duration'      => 'required|integer',
            'instructors'   => 'required|array',
        ]);

        $discipline->update(request()->only('name', 'basic', 'duration'));

        $discipline->attachInstructors(request()->instructors);

        session()->flash('status', 'Disciplina atualizada com sucesso!');

        return view('disciplines.show', compact('discipline'));
    }
}
