<?php

namespace App\View\Components\Lesson;

use App\Models\Lesson;
use Illuminate\View\Component;

class ForWeekList extends Component
{
    public $lessons;

    public $title;

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
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.lesson.for-week-list');
    }

    public function listForInstructor()
    {
        return $this->user->isInstructor();
    }

    public function showRegisterButton(Lesson $lesson)
    {
        return (
            ! $lesson->isRegistered() 
            && $lesson->isForToday()
            && $this->user->isInstructor()
        ) 
        ? true 
        : false;
    }

    public function showClasses(Lesson $lesson)
    {
        return $this->user->isNovice() ? $this->user->class : $lesson->formatted_course_classes;
    }

    private function setLessons()
    {
        if ($this->user->isInstructor()) {
            $this->lessons = Lesson::week()->forInstructor($this->user)->oldest('date')->get();
        } else if ($this->user->isEmployer()) {
            $this->lessons = Lesson::week()->forEmployer($this->user)->oldest('date')->get();
        } else {
            $this->lessons = Lesson::week()->forNovice($this->user)->oldest('date')->get();
        }
    }

    private function setTitle(string $title)
    {
        if ($this->lessons->count() === 0) {
            $this->title = 'Nenhuma aula para esta semana';
        } else {
            $this->title = $title;
        }
    }
}
