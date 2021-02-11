<?php

namespace App\Http\Controllers;

use App\Models\Holiday;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::oldest('date')->get();

        return view('holidays.index', compact('holidays'));
    }

    public function create()
    {
        return view('holidays.create');
    }

    public function store()
    {
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
