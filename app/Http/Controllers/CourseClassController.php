<?php

namespace App\Http\Controllers;

use App\Models\CourseClass;

class CourseClassController extends Controller
{
    public function index()
    {
        abort_if(request()->user()->cannot('viewAny', CourseClass::class), 401);

        $courseClasses = CourseClass::all();

        return view('classes.index', compact('courseClasses'));
    }

    public function show(CourseClass $courseClass)
    {
        abort_if(request()->user()->cannot('view', $courseClass), 401);

        return view('classes.show', compact('courseClass'));
    }

    public function create()
    {
        abort_if(request()->user()->cannot('create', CourseClass::class), 401);

        return view('classes.create');
    }
}
