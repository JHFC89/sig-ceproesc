<?php

namespace App\Http\Controllers;

use App\Models\CourseClass;

class CourseClassController extends Controller
{
    public function show(CourseClass $courseClass)
    {
        return view('classes.show', compact('courseClass'));
    }
}
