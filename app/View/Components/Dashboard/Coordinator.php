<?php

namespace App\View\Components\Dashboard;

use App\Models\LessonRequest;
use Illuminate\View\Component;

class Coordinator extends Component
{
    public $coordinator;

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

        $this->coordinator = request()->user();

        $this->requests = LessonRequest::whereUnsolved()->get();

        $this->requests->load('lesson');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.coordinator');
    }
}
