<?php

namespace App\View\Components\Lesson;

use App\Models\Lesson;
use Illuminate\View\Component;

class ForWeekList extends Component
{
    public $lessons;
    public $title;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title = '', $user)
    {
        $this->lessons = Lesson::week()->where('instructor_id', $user->id)->get();

        if ($this->lessons->count() === 0) {
            $this->title = 'Nenhuma aula para esta semana';
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
        return view('components.lesson.for-week-list');
    }
}
