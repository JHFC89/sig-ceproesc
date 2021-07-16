<?php

namespace App\Http\Livewire;

use Livewire\Component;

class DestroyRegistration extends Component
{
    public $registration;

    public $authorized;

    public $status;

    public function render()
    {
        return view('livewire.destroy-registration');
    }

    public function authorized()
    {
        return $this->authorized && !$this->registration->invitation->hasBeenUsed();
    }

    public function destroy()
    {
        $this->status = 'confirmation';
    }

    public function abort()
    {
        $this->status = 'aborted';
    }

    public function confirmation()
    {
        return $this->status === 'confirmation';
    }

    public function success()
    {
        return $this->status === 'success';
    }

    public function failed()
    {
        return $this->status === 'failed';
    }

    public function showConfirmation()
    {
        return $this->status === 'confirmation'
            || $this->status === 'success'
            || $this->status === 'failed';
    }

    public function confirm()
    {
        if (!$this->authorized) {
            return;
        }

        $success = $this->registration->delete();

        if ($success) {
            $this->status = 'success';
        } else {
            $this->status = 'failed';
        }
    }
}
