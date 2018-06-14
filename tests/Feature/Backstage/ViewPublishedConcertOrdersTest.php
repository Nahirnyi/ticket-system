<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/14/18
 * Time: 12:51 PM
 */

namespace Tests\Feature\Backstage;

use OrderFactory;
use App\User;
use ConcertFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ViewPublishedConcertOrdersTest extends  TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_promoter_can_view_the_orders_of_their_own_published_concert()
    {
        $user = factory(User::class)->create();
        $concert = ConcertFactory::createPublished(['user_id' => $user->id]);

        $order = OrderFactory::createPublished($concert);

        $response = $this->actingAs($user)->get("/backstage/published-concerts/{$concert->id}/orders");
        $response->assertStatus(200);
        $response->assertViewIs('backstage.published-concert-orders.index');
    }

    /** @test */
    function a_promoter_cannot_view_the_orders_of_unpublished_concerts()
    {
        $user = factory(User::class)->create();
        $concert = ConcertFactory::createUnpublished(['user_id' => $user->id]);
        $response = $this->actingAs($user)->get("/backstage/published-concerts/{$concert->id}/orders");
        $response->assertStatus(404);
    }

    /** @test */
    function a_promoter_cannot_view_the_orders_of_another_published_concerts()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $concert = ConcertFactory::createPublished(['user_id' => $otherUser->id]);
        $response = $this->actingAs($user)->get("/backstage/published-concerts/{$concert->id}/orders");
        $response->assertStatus(404);
    }

    /** @test */
    function a_guest_cannot_view_the_orders_of_any_published_concerts()
    {
        $concert = ConcertFactory::createPublished();
        $response = $this->get("/backstage/published-concerts/{$concert->id}/orders");
        $response->assertRedirect('/login');
    }
}