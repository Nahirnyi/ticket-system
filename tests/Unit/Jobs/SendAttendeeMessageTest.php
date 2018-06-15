<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/15/18
 * Time: 9:26 AM
 */

namespace Tests\Unit\Jobs;

use App\Mail\AttendeeMessageEmail;
use App\Jobs\SendAttendeeMessage;
use Illuminate\Support\Facades\Mail;
use OrderFactory;
use ConcertFactory;
use Tests\TestCase;
use App\AttendeeMessage;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SendAttendeeMessageTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function it_sends_the_message_to_all_concert_attendee()
    {
        $this->disableExceptionHandling();
        Mail::fake();
        $concert = ConcertFactory::createPublished();

        $message = AttendeeMessage::create([
            'concert_id' => $concert->id,
            'subject' => 'My subject',
            'message' => 'My message'
        ]);

        OrderFactory::createPublished($concert, ['email' => 'alex@example.com']);
        OrderFactory::createPublished($concert, ['email' => 'sam@example.com']);
        OrderFactory::createPublished($concert, ['email' => 'taylor@example.com']);

        SendAttendeeMessage::dispatch($message);
        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('alex@example.com')
                && $mail->attendeeMessage->is($message);
        });

        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('sam@example.com')
                && $mail->attendeeMessage->is($message);
        });

        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('taylor@example.com')
                && $mail->attendeeMessage->is($message);
        });

    }
}