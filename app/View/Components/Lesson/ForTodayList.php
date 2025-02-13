<?php

namespace App\View\Components\Lesson;

use App\Models\Lesson;
use Illuminate\View\Component;

class ForTodayList extends Component
{
    public $lessons;

    public $hasLesson;

    public $title;

    public $columnSize;

    public $hideRegistered;

    public $alwaysShow;

    protected $user;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($user, $title = '', $hideRegistered = false, $alwaysShow = true)
    {
        $this->user = $user;

        $this->hideRegistered = $hideRegistered;

        $this->setLessons();

        $this->hasLesson = $this->lessons->count() > 0;

        $this->setTitle($title);

        $this->setColumnsSizes();

        $this->alwaysShow = $alwaysShow;
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
        return $this->user->can('createRegister', $lesson);
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
        } else if ($this->user->isNovice()) {
            $this->lessons = Lesson::today()->forNovice($this->user)->get();
        } else {
            $this->lessons = Lesson::today()->get();
        }
    }

    private function setTitle(string $title)
    {
        if ($this->hasLesson) {
            $this->title = $title;

            return;
        }

        $this->title = 'Nenhuma aula para hoje';
    }
    
    private function setColumnsSizes()
    {
        if ($this->user->isInstructor()) {
            return $this->columnSize = $this->columnSizeForInstructor();
        }

        if ($this->user->isEmployer()) {
            return $this->columnSize = $this->columnSizeForEmployer();
        }

        return $this->columnSize = $this->columnSizeForNovice();
    }

    private function columnSizeForInstructor()
    {
        return [
            'class'         => $this->hideRegistered ? 'col-span-8' : 'col-span-5',
            'discipline'    => $this->hideRegistered ? 'col-span-2' : 'col-span-1',
            'registered'    => 'col-span-4',
            'actions'       => 'col-span-2',
        ];
    }

    private function columnSizeForEmployer()
    {
        return [
            'class'         => $this->hideRegistered ? 'col-span-7' : 'col-span-4',
            'discipline'    => 'col-span-2',
            'instructor'    => 'col-span-2',
            'registered'    => 'col-span-2',
            'actions'       => 'col-span-2',
            'actions'       => $this->hideRegistered ? 'col-span-1' : 'col-span-2',
        ];
    }

    private function columnSizeForNovice()
    {
        return [
            'class'         => $this->hideRegistered ? 'col-span-3' : 'col-span-2',
            'discipline'    => $this->hideRegistered ? 'col-span-3' : 'col-span-2',
            'instructor'    => $this->hideRegistered ? 'col-span-3' : 'col-span-2',
            'registered'    => 'col-span-4',
            'actions'       => $this->hideRegistered ? 'col-span-3' : 'col-span-2',
        ];
    }
}
