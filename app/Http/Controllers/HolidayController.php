<?php

namespace App\Http\Controllers;

use App\Models\Holiday;

class HolidayController extends Controller
{
    public function index()
    {
        abort_if(request()->user()->cannot('viewAny', Holiday::class), 401);

        $holidays = Holiday::oldest('date')->get();

        return view('holidays.index', compact('holidays'));
    }

    public function create()
    {
        abort_if(request()->user()->cannot('create', Holiday::class), 401);

        return view('holidays.create');
    }

    public function store()
    {
        abort_if(request()->user()->cannot('create', Holiday::class), 401);

        $holidays = collect(request()->holidays);
        $holidays->each(function ($holiday) {
            Holiday::create([
                'name' => $holiday['name'],
                'date' => Holiday::formatDateToCreate($holiday),
            ]);
        });

        $holidays = Holiday::oldest('date')->get();

        session()->flash('status', 'Feriados cadastrados com sucesso!');

        return view('holidays.index', compact('holidays'));
    }
}
