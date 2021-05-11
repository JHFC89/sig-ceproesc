<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;

class Employer extends Component
{
    public $employer;

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

        $this->employer = request()->user();
    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.employer');
    }
}
