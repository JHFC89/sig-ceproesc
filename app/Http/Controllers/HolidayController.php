<?php

namespace App\Http\Controllers;

use App\Models\Holiday;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::all();

        return view('holidays.index', compact('holidays'));
    }
}
