<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;

class Novice extends Component
{
    public $novice;

    public $show;

    public $frequency;

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

        $this->novice = request()->user();

        $this->frequency = $this->novice
                                ->courseClass
                                ->noviceFrequency($this->novice);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.novice');
    }
}
