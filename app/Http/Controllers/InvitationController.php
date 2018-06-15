<?php

namespace App\Http\Controllers;

use App\Invitation;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function show($code)
    {
        $invitation = Invitation::findByCode($code);

        abort_if($invitation->hasBeenUsed(), 404);

        return view('invitation.show', [
            'invitation' => $invitation
        ]);
    }
}
