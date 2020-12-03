<?php

namespace App\View\Components\Lesson;

use App\Models\Lesson;
use Illuminate\View\Component;

class ForTodayList extends Component
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

        if ($this->user->isInstructor()) {
            $this->lessons = Lesson::today()->where('instructor_id', $this->user->id)->get();
        } else if ($this->user->isEmployer()) {
            $this->lessons = Lesson::today()->enrolledNovices($this->user->novices->pluck('id')->toArray())->get();
        } 
        else {
            $this->lessons = Lesson::today()->enrolled($this->user->id)->get();
        }

        if ($this->lessons->count() === 0) {
            $this->title = 'Nenhuma aula para hoje';
        } else {
            $this->title = $title;
        }
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
}
