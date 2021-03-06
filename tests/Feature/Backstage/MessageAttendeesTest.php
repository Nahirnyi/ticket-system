<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/14/18
 * Time: 4:57 PM
 */

namespace Tests\Feature\Backstage;

use App\Jobs\SendAttendeeMessage;
use App\AttendeeMessage;
use App\User;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use ConcertFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;

class MessageAttendeesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    /*function a_promoter_can_view_the_message_form_for_their_own_concert()
    {
        $user = factory(User::class)->create();
        $concert = ConcertFactory::createPublished([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get("/backstage/concerts/{$concert->id}/messages/new");


        $response->assertStatus(200);
        $response->assertViewIs('backstage.concert-messages.new');
    }*/

    /** @test */
    function a_promoter_cannot_view_the_message_form_for_another_concert()
    {
        $user = factory(User::class)->create();
        $concert = ConcertFactory::createPublished([
            'user_id' => factory(User::class)->create()->id,
        ]);

        $response = $this->actingAs($user)->get("/backstage/concerts/{$concert->id}/messages/new");

        $response->assertStatus(404);
    }

    /** @test */
    function a_guests_cannot_view_the_message_form_for_any_concert()
    {
        $concert = ConcertFactory::createPublished();

        $response = $this->get("/backstage/concerts/{$concert->id}/messages/new");

        $response->assertRedirect('/login');
    }

    /** @test */
    function a_promoter_can_send_a_new_message()
    {
        Queue::fake();
        $user = factory(User::class)->create();
        $concert = ConcertFactory::createPublished([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->post("/backstage/concerts/{$concert->id}/messages", [
            'subject' => 'My subject',
            'message' => 'My message',
        ]);


        $response->assertRedirect("/backstage/concerts/{$concert->id}/messages/new");
        $response->assertSessionHas('flash');

        $message = AttendeeMessage::first();
        $this->assertEquals($concert->id, $message->concert_id);
        $this->assertEquals('My message', $message->message);
        $this->assertEquals('My subject', $message->subject);

        Queue::assertPushed(SendAttendeeMessage::class, function ($job) use ($message) {
            return $job->attendeeMessage->is($message);
        });
    }
    /** @test */
    function a_promoter_cannot_send_a_new_message_for_other_concerts()
    {
        Queue::fake();
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $concert = ConcertFactory::createPublished([
            'user_id' => $otherUser->id,
        ]);
        $response = $this->actingAs($user)->post("/backstage/concerts/{$concert->id}/messages", [
            'subject' => 'My subject',
            'message' => 'My message',
        ]);
        $response->assertStatus(404);
        $this->assertEquals(0, AttendeeMessage::count());
        Queue::assertNotPushed(SendAttendeeMessage::class);
    }
    /** @test */
    function a_guest_cannot_send_a_new_message_for_any_concerts()
    {
        Queue::fake();
        $concert = ConcertFactory::createPublished();
        $response = $this->post("/backstage/concerts/{$concert->id}/messages", [
            'subject' => 'My subject',
            'message' => 'My message',
        ]);
        $response->assertRedirect('/login');
        $this->assertEquals(0, AttendeeMessage::count());
        Queue::assertNotPushed(SendAttendeeMessage::class);
    }

}