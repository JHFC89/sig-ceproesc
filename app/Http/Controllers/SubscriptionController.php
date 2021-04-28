<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CourseClass;

class SubscriptionController extends Controller
{
    public function create(CourseClass $courseClass)
    {
        abort_unless(request()->user()->isCoordinator(), 401);

        $this->checkCourseClassLessons($courseClass);

        $availableNovices = $this->availableNovices();

        $this->checkAvailableNovices($availableNovices);

        return view(
            'subscriptions.create',
            compact('courseClass', 'availableNovices')
        );
    }

    public function store()
    {
        abort_unless(request()->user()->isCoordinator(), 401);

        $data = request()->validate([
            'class'     => ['required', 'exists:App\Models\CourseClass,id'],
            'novices'   => ['required'],
        ]);

        $courseClass = CourseClass::find($data['class']);

        $novices = $this->novices($data['novices']);

        $novicesToSubscribe = $novices->reject->isSubscribed();

        $novicesToSubscribe->each->subscribeToClass($courseClass);

        $unavailableNovices = $novices->diff($novicesToSubscribe);

        $this->setSuccessMessage($novicesToSubscribe);

        $this->setErrorMessage($unavailableNovices);

        $availableNovices = $this->availableNovices();

        $this->checkAvailableNovices($availableNovices);

        return view(
            'subscriptions.create',
            compact('courseClass', 'availableNovices')
        );
    }

    private function checkCourseClassLessons(CourseClass $courseClass)
    {
        if (! $courseClass->hasLessons()) {
            session()->flash(
                'no-lessons',
                'Ainda não há aulas cadastradas para essa turma.'
            );
        }
    }

    private function availableNovices()
    {
        return User::whereNovice()->whereAvailableToSubscribe()->get();
    }

    private function checkAvailableNovices($availableNovices)
    {
        if ($availableNovices->count() === 0) {
            session()->flash(
                'no-novices',
                'Não há aprendizes disponíveis para matricular.'
            );
        }
    }

    private function novices(array $novices_ids)
    {
        $novices_ids = collect($novices_ids)->flatten()->toArray();

        return User::whereIn('id', $novices_ids)->get();
    }

    private function setSuccessMessage($novicesToSubscribe)
    {
        if ($novicesToSubscribe->count() > 0) {
            session()->flash('status', 'Aprendizes matriculados com sucesso!');
        }
    }

    private function setErrorMessage($unavailableNovices)
    {
        if ($unavailableNovices->count() > 0) {
            $message =  'Aprendizes já matriculados em outra turma:';

            $names = implode(
                ', ',
                $unavailableNovices->pluck('name')->toArray()
            );

            $fullMessage = "{$message} ${names}.";

            session()->flash('error', $fullMessage);
        }
    }
}
