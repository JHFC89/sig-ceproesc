<?php

namespace App\Http\Controllers;

use App\Models\Invitation;

class SendInvitationController extends Controller
{
    public function __invoke(Invitation $invitation)
    {
        abort_unless(request()->user()->isCoordinator(), 401);

        abort_if($invitation->hasBeenUsed(), 404);

        $invitation->send();

        session()->flash('status', 'E-mail de registro enviado com sucesso!');

        return redirect()->back();
    }
}
