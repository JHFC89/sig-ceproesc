<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CreateLessonRow extends Component
{
    public $lesson = null;

    public $date;

    public $type;

    public $duration;

    public $disciplines;

    public $completedDisciplines;

    public $selectedDiscipline = null;

    public $instructors;

    public $selectedInstructor = null;

    public $preSelectedInstructors = [];

    protected $listeners = [
        'disciplineCompleted', 
        'disciplineUncompleted',
        'preSelectedInstructorsUpdated',
        'fixSelectBug',
    ];

    public function mount()
    {
        if (isset($this->lesson)) {
            $this->selectedDiscipline = $this->lesson['discipline_id'];
            $this->selectedInstructor = $this->lesson['instructor_id'];
            $this->showSelectedDisciplineInstructors($this->selectedDiscipline);
        }
    }

    public function preSelectedInstructorsUpdated($preSelectedInstructors)
    {
        $this->preSelectedInstructors = $preSelectedInstructors;
    }

    public function updatedSelectedDiscipline($id)
    {
        if ($this->isUpdating()) {
            $this->selectedInstructor = null;
        }

        $this->showSelectedDisciplineInstructors($id);

        if (isset($this->preSelectedInstructors["discipline-${id}"])) {
            $this->selectedInstructor = $this->preSelectedInstructors["discipline-${id}"];
            $this->emitLessonAdded();
        }
    }

    public function updatedSelectedInstructor()
    {
        $this->emitLessonAdded();
    }

    public function showSelectedDisciplineInstructors($id)
    {
        $this->instructors = $this->disciplines->find($id)->instructors;
    }

    private function emitLessonAdded()
    {
        $this->lesson = [
            'id'            => $this->date->format('Y-m-d'). '-' . $this->type,
            'date'          => $this->date->format('Y-m-d'),
            'type'          => $this->type,
            'duration'      => $this->duration,
            'discipline_id' => $this->selectedDiscipline,
            'instructor_id' => $this->selectedInstructor,
        ];

        $this->emitUp('lessonAdded', $this->lesson);
    }

    public function isUpdating()
    {
        return isset($this->selectedDiscipline)
            && isset($this->selectedInstructor);
    }

    public function resetLesson()
    {
        $this->selectedDiscipline = null;
        $this->selectedInstructor = null;

        $lesson = $this->lesson;
        $this->lesson = null;

        $this->emitUp('lessonReseted', $lesson);
    }

    public function isCompleted(int $discipline)
    {
        return in_array($discipline, $this->completedDisciplines);
    }

    public function disciplineCompleted($completedDisciplines)
    {
        $this->completedDisciplines = $completedDisciplines;
    }

    public function disciplineUncompleted($completedDisciplines)
    {
        $this->completedDisciplines = $completedDisciplines;
    }

    public function render()
    {
        return view('livewire.create-lesson-row');
    }

    public function fixSelectBug()
    {
        return;
    }

}
