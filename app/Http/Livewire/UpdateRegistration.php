<?php

namespace App\Http\Livewire;

use Livewire\Component;

class UpdateRegistration extends Component
{
    public $label;

    public $type;

    public $property;

    public $registration;

    public $editing = false;

    public $fields = [];

    public $updatable = false;

    protected $rules = [
        'fields.email' => [
            'required',
            'email',
            'unique:users,email',
            'unique:invitations,email',
        ],
        'fields.cpf' => [
            'required',
            'size:14',
            'unique:registrations,cpf',
        ]
    ];

    public function mount()
    {
        $this->fields[$this->property] = null;
    }

    public function render()
    {
        return view('livewire.update-registration');
    }

    protected function validationAttributes()
    {
        $key = $this->fieldName();

        return [$key => $this->label];
    }

    public function toggleEdit()
    {
        $this->fields[$this->property] = null;

        $this->editing = !$this->editing;
    }

    public function update()
    {
        if (!$this->updatable()) {
            return;
        }

        $this->validateOnly($this->fieldName());

        $this->registration->{$this->property} = $this->fields[$this->property];

        $this->registration->push();

        $this->editing = false;
    }

    public function placeholder()
    {
        return $this->registration->{$this->property};
    }

    public function updatable()
    {
        return $this->updatable && !$this->registration->invitation->hasBeenUsed();
    }

    public function fieldName()
    {
        return 'fields.' . $this->property;
    }
}
