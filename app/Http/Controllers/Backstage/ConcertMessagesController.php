<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/14/18
 * Time: 5:14 PM
 */

namespace App\Http\Controllers\Backstage;

use App\Jobs\SendAttendeeMessage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ConcertMessagesController extends Controller
{
    public function create($id)
    {
        $concert = Auth::user()->concerts()->findOrFail($id);
        return view('backstage.concert-messages.new',['concert' => $concert]);
    }

    public function store($id)
    {
        $concert = Auth::user()->concerts()->findOrFail($id);

        $this->validate(request(), [
            'subject' => ['required'],
            'message' => ['required'],
        ]);

        $message = $concert->attendeeMessage()->create(request(['subject', 'message']));

        SendAttendeeMessage::dispatch($message);

        return redirect()
            ->route('backstage.concert-messages.new', ['concert' => $concert])
            ->with('flash','Your message send');
    }

}