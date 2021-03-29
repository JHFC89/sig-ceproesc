<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CreateLessonDisciplineDurationCounter extends Component
{
    public $courseClass;

    public $disciplinesDuration = null;

    public $preSelectedInstructors;

    protected $listeners = ['disciplinesDurationUpdated'];

    public function mount()
    {
        $this->preSelectedInstructors = $this->preSeletectedInstructorsList();
    }

    private function preSeletectedInstructorsList()
    {
        return $this->courseClass
                    ->course
                    ->disciplines
                    ->flatMap(function ($discipline) {
                        return ['discipline-' . $discipline->id => null];
                    });
    }

    public function updatedPreSelectedInstructors()
    {
        $this->emit(
            'preSelectedInstructorsUpdated', 
            $this->preSelectedInstructors
        );
    }

    public function disciplinesDurationUpdated($disciplinesDuration)
    {
        $this->disciplinesDuration = $disciplinesDuration;
    }

    public function durationDiff($discipline)
    {
        $diff = $this->disciplinesDuration[$discipline->id];

        return $diff === null ? $discipline->duration : $diff; 
    }

    public function render()
    {
        return view('livewire.create-lesson-discipline-duration-counter');
    }
}
