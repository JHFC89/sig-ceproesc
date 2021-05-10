<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;

class Instructor extends Component
{
    public $instructor;

    public $show;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(bool $show)
    {
        if (! $show) {
            return;
        }

        $this->show = $show;

        $this->instructor = request()->user();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.instructor');
    }
}
