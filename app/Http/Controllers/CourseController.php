<?php

namespace App\Http\Controllers;

use App\Models\Course;

class CourseController extends Controller
{
    public function show(Course $course)
    {
        abort_if(request()->user()->cannot('view', $course), 401);

        return view('courses.show', compact('course'));
    }
}
