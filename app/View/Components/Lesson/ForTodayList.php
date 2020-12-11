<?php

namespace App\View\Components\Lesson;

use App\Models\Lesson;
use Illuminate\View\Component;

class ForTodayList extends Component
{
    public $lessons;

    public $title;

    public $headerClasses;

    protected $user;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title = '', $user)
    {
        $this->user = $user;

        $this->setLessons();

        $this->setTitle($title);

        $this->setHeaderClasses();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.lesson..for-today-list');
    }

    public function listForInstructor()
    {
        return $this->user->isInstructor();
    }

    public function showRegisterButton(Lesson $lesson)
    {
        return (! $lesson->isRegistered() && $this->user->isInstructor()) ? true : false;
    }

    public function showClasses(Lesson $lesson)
    {
        return $this->user->isNovice() ? $this->user->class : $lesson->formatted_course_classes;
    }

    private function setLessons()
    {
        if ($this->user->isInstructor()) {
            $this->lessons = Lesson::today()->forInstructor($this->user)->get();
        } else if ($this->user->isEmployer()) {
            $this->lessons = Lesson::today()->forEmployer($this->user)->get();
        } else {
            $this->lessons = Lesson::today()->forNovice($this->user)->get();
        }
    }

    private function setTitle(string $title)
    {
        if ($this->lessons->count() === 0) {
            $this->title = 'Nenhuma aula para hoje';
        } else {
            $this->title = $title;
        }
    }
    
    private function setHeaderClasses()
    {
        if ($this->user->isInstructor()) {
            return $this->headerClasses = $this->headerClassesForInstructor();
        }

        if ($this->user->isEmployer()) {
            return $this->headerClasses = $this->headerClassesForEmployer();
        }

        return $this->headerClasses = $this->headerClassesForNovice();
    }

    private function headerClassesForInstructor()
    {
        return [
            'class'         => 'col-span-5',
            'discipline'    => 'col-span-1',
            'registered'    => 'col-span-4',
            'actions'       => 'col-span-2',
        ];
    }

    private function headerClassesForEmployer()
    {
        return [
            'class'         => 'col-span-4',
            'discipline'    => 'col-span-2',
            'instructor'    => 'col-span-2',
            'registered'    => 'col-span-2',
            'actions'       => 'col-span-2',
        ];
    }

    private function headerClassesForNovice()
    {
        return [
            'class'         => 'col-span-2',
            'discipline'    => 'col-span-2',
            'instructor'    => 'col-span-2',
            'registered'    => 'col-span-4',
            'actions'       => 'col-span-2',
        ];
    }
}
