<?php

namespace App\Http\Controllers;

use App\Models\Registration;

class NoviceFrequencyController extends Controller
{
    public function show(Registration $registration)
    {
        $user = request()->user();

        if ($user->isNovice()) {
            abort_unless($user->is($registration->user), 401);
        } else {
            abort_if($user->cannot('view', $registration), 401);
        }

        abort_unless($registration->isForNovice(), 404);

        $courseClass = optional($registration->user)->courseClass;

        abort_if($courseClass === null, 404);

        $frequency = $courseClass->noviceFrequency($registration->user);

        $lessons = $courseClass->lessons()->oldest('date')->paginate();

        return view('frequencies.show', compact(
            'registration',
            'frequency',
            'lessons'
        ));
    }
}
