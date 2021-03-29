<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CreateLessonForm extends Component
{
    public $courseClass;

    public $preSelectedInstructors = [];

    public $disciplines;

    public $disciplinesDuration;

    public $completedDisciplines = [];

    public $showDisciplineCompletedMessage = false;

    public $months;

    public $month;

    public $nextMonthAvailable = true;

    public $prevMonthAvailable = false;

    public $lessons = [];

    protected $listeners = [
        'preSelectedInstructorsUpdated',
        'lessonAdded', 
        'lessonReseted',
    ];

    public $createLessonsAvailable = false;

    public $createdLessons;

    public function mount($courseClass)
    {
        $this->disciplines = $courseClass->course->disciplines;

        $this->disciplinesDuration = $this->disciplines->reduce(function ($disciplines, $discipline) {
            $disciplines[$discipline->id] = $discipline->duration;
            return $disciplines;
        }, []);

        $this->months = $courseClass->allMonths();

        $this->month = $this->months->first();
    }

    public function lessonForDate($date, string $type)
    {
        return collect($this->lessons)
            ->filter(function ($lesson) use ($date, $type) {
                return $lesson['type'] === $type 
                    && $lesson['date'] === $date->format('Y-m-d');
        })->first();
    }

    public function dates()
    {
        return $this->courseClass
                    ->theoreticalDaysForMonth(
                        $this->month['month'], 
                        $this->month['year']
                    );
    }

    public function nextMonth()
    {
        if ($this->nextMonthAvailable === false) {
            return;
        }

        $this->prevMonthAvailable = true;

        $nextMonth = $this->getActualMonthKey() + 1;

        if ($this->months->count() == ($nextMonth + 1)) {
            $this->nextMonthAvailable = false;
        }

        $this->month = $this->months[$nextMonth];
    }

    public function prevMonth()
    {
        if ($this->prevMonthAvailable === false) {
            return;
        }

        $this->nextMonthAvailable = true;

        $prevMonth = $this->getActualMonthKey() - 1;

        if ($prevMonth === 0) {
            $this->prevMonthAvailable = false;
        }

        $this->month = $this->months[$prevMonth];
    }

    private function getActualMonthKey()
    {
        return $this->months->search(function ($month) {
            return $month['id'] == $this->month['id'];
        });
    }

    public function unavailableClassList(string $type)
    {

        if ($type == 'next' && $this->nextMonthAvailable) {
            return '';
        }

        if ($type == 'prev' && $this->prevMonthAvailable) {
            return '';
        }

        return 'bg-gray-200 text-gray-400 pointer-events-none';
    } 

    public function calculateDuration($date, $type = 'first')
    {
        // TODO: CourseClass method to calculate the lesson duration
        
        if ($date->is($this->courseClass->first_day)) {
            $duration = $this->courseClass->first_duration;
        } elseif ($date->is($this->courseClass->second_day)) {
            $duration = $this->courseClass->second_duration;
        } else {
            $duration = $this->courseClass->first_duration;
        }

        if ($duration === 4 || $duration === 5 && $type === 'first') {
            $duration = 2;
        } elseif ($duration === 5 && $type === 'second') {
            $duration = 3;
        }

        return $duration;
    }

    public function preSelectedInstructorsUpdated($preSelectedInstructors)
    {
        $this->preSelectedInstructors = $preSelectedInstructors;
    }

    public function lessonAdded($lesson)
    {
        $lesson_id = $lesson['id'];

        $prevDiscipline = $this->lessons[$lesson_id]['discipline_id'] ?? false; 

        $this->lessons[$lesson_id] = $lesson;

        $this->updateDisciplinesDuration($lesson, $prevDiscipline);
    }

    public function lessonReseted($lesson)
    {
        unset($this->lessons[$lesson['id']]);

        $this->setDisciplineDuration($lesson['discipline_id']);
    }

    private function updateDisciplinesDuration(array $lesson, $prevDiscipline)
    {
        if ($prevDiscipline) {
            $this->setDisciplineDuration($prevDiscipline);
        }

        $this->setDisciplineDuration($lesson['discipline_id']);
    }

    private function setDisciplineDuration($discipline)
    {
        $assignedDuration = $this->calculateAssignedDuration($discipline);

        $disciplineDuration = $this->disciplines->find($discipline)->duration;

        $updatedDuration = $disciplineDuration - $assignedDuration;

        $previousDuration = $this->disciplinesDuration[$discipline];

        $this->disciplinesDuration[$discipline] = $updatedDuration;

        $this->emit('disciplinesDurationUpdated', $this->disciplinesDuration);

        if ($previousDuration === 0) {
            $key = array_search($discipline, $this->completedDisciplines);
            unset($this->completedDisciplines[$key]);

            $this->emit('disciplineUncompleted', $this->completedDisciplines);
        }

        if ($updatedDuration === 0) {
            array_push($this->completedDisciplines, $discipline);

            $this->emit('disciplineCompleted', $this->completedDisciplines);

            $this->showDisciplineCompletedMessage($discipline);
        }

        $this->checkLessonsCanBeCreated();
    }

    private function calculateAssignedDuration(int $discipline)
    {
        return collect($this->lessons)
            ->where('discipline_id', $discipline)
            ->sum('duration');
    }

    public function showDisciplineCompletedMessage($discipline)
    {
        $this->showDisciplineCompletedMessage = $discipline;
    }

    private function checkLessonsCanBeCreated()
    {
        if (! $this->allDisciplinesAreCompleted()) {
            $this->createLessonsAvailable = false;

            return; 
        }

        if (! $this->allLessonDatesAreAssigned()) {
            $this->createLessonsAvailable = false;
        
           return; 
        }

        if ($this->courseClass->hasLessons()) {
            return;
        }

        $this->createLessonsAvailable = true;
    }

    private function allDisciplinesAreCompleted()
    {
        return count($this->completedDisciplines) == $this->disciplines->count() 
            && collect($this->disciplinesDuration)->sum() == 0;
    }

    private function allLessonDatesAreAssigned()
    {
        return count($this->lessons) 
            == ($this->courseClass->allTheoreticalDays()->count() * 2);
    }

    public function createLessons()
    {
        $lessons = $this->courseClass->createLessonsFromArray($this->lessons);

        $this->createdLessons = $lessons;
    }

    public function hideDisciplineCompletedMessage()
    {
        $this->showDisciplineCompletedMessage = false;
        $this->emit('fixSelectBug');
    }

    public function getCompletedDiscipline()
    {
        $id = $this->showDisciplineCompletedMessage;
        return $this->disciplines->find($id);
    }

    public function render()
    {
        return view('livewire.create-lesson-form');
    }
}
