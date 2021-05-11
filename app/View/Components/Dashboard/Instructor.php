<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;

class Instructor extends Component
{
    public $instructor;

    public $show;

    public $requests;

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

        $this->requests = $this->instructor->unsolvedRequests();

        $this->requests->load('lesson');
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
