<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/14/18
 * Time: 10:40 AM
 */

namespace Tests\Feature\Backstage;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Concert;
use App\User;
use Tests\TestCase;
use ConcertFactory;

class PublishConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_promoter_can_publish_their_own_concert()
    {
        $user = factory(User::class)->create();
        $concert = ConcertFactory::createPublished([
            'user_id' => $user->id,
            'ticket_quantity' => 3,
        ]);

        $response = $this->actingAs($user)->post('/backstage/published-concerts', [
            'concert_id' => $concert->id,
        ]);

        $response->assertStatus(422);
        $this->assertEquals(3, $concert->fresh()->ticketsRemaining());
    }

    /** @test */
    function a_concert_can_only_be_published_once()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->states('unpublished')->create([
            'user_id' => $user->id,
            'ticket_quantity' => 3,
        ]);

        $response = $this->actingAs($user)->post('/backstage/published-concerts', [
            'concert_id' => $concert->id,
        ]);

        $response->assertRedirect('/backstage/concerts');
        $concert = $concert->fresh();
        $this->assertTrue($concert->isPublished());
        $this->assertEquals(3, $concert->ticketsRemaining());
    }
}