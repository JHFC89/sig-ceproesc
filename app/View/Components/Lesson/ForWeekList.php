<?php

namespace App\View\Components\Lesson;

use App\Models\Lesson;
use Illuminate\View\Component;

class ForWeekList extends Component
{
    public $lessons;

    public $hasLesson;

    public $title;

    public $columnSize;

    public $alwaysShow;

    protected $user;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title = '', $user, $alwaysShow = true)
    {
        $this->user = $user;

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
        return view('components.lesson.for-week-list');
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

    public function showExpirationRequestButton(Lesson $lesson)
    {
        return $this->user->can('createForLesson', [LessonRequest::class, $lesson])
            && $lesson->isExpired();
    }

    public function showOpenRequestWarning(Lesson $lesson)
    {
        return $lesson->hasOpenRequest() && $this->user->isInstructor();
    }

    public function showPendingRequestWarning(Lesson $lesson)
    {
        return $lesson->hasPendingRequest() && $this->user->can('createRegister', $lesson);
    }

    private function setLessons()
    {
        if ($this->user->isInstructor()) {
            $this->lessons = Lesson::week()->forInstructor($this->user)->oldest('date')->get();
        } else if ($this->user->isEmployer()) {
            $this->lessons = Lesson::week()->forEmployer($this->user)->oldest('date')->get();
        } else if ($this->user->isNovice()){
            $this->lessons = Lesson::week()->forNovice($this->user)->oldest('date')->get();
        } else {
            $this->lessons = Lesson::week()->oldest('date')->get();
        }
    }

    private function setTitle(string $title)
    {
        if ($this->hasLesson) {
            $this->title = $title;

            return;
        }

        $this->title = 'Nenhuma aula para esta semana';
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
            'date'          => 'col-span-2',
            'class'         => 'col-span-4',
            'discipline'    => 'col-span-1',
            'registered'    => 'col-span-3',
            'actions'       => 'col-span-2',
        ];
    }

    private function columnSizeForEmployer()
    {
        return [
            'date'          => 'col-span-2',
            'class'         => 'col-span-4',
            'discipline'    => 'col-span-2',
            'instructor'    => 'col-span-1',
            'registered'    => 'col-span-2',
            'actions'       => 'col-span-1',
        ];
    }

    private function columnSizeForNovice()
    {
        return [
            'date'          => 'col-span-2',
            'class'         => 'col-span-2',
            'discipline'    => 'col-span-2',
            'instructor'    => 'col-span-2',
            'registered'    => 'col-span-2',
            'actions'       => 'col-span-2',
        ];
    }
}
