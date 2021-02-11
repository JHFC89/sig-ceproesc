<?php

namespace App\Http\Livewire;

use Livewire\Component;

class HolidayForm extends Component
{
    public $count = 0;

    public function increment()
    {
        $this->count++;

        $this->render();
    }

    public function render()
    {
        return view('livewire.holiday-form');
    }
}
