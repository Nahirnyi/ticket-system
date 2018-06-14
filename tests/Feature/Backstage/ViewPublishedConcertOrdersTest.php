<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/14/18
 * Time: 12:51 PM
 */

namespace Tests\Feature\Backstage;

use Carbon\Carbon;
use OrderFactory;
use App\User;
use ConcertFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Database\Eloquent\Collection;

class ViewPublishedConcertOrdersTest extends  TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_promoter_can_view_the_orders_of_their_own_published_concert()
    {
        $user = factory(User::class)->create();
        $concert = ConcertFactory::createPublished(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/backstage/published-concerts/{$concert->id}/orders");
        $response->assertStatus(200);
        $response->assertViewIs('backstage.published-concert-orders.index');
        $this->assertTrue($response->data('concert')->is($concert));

    }

    /** @test */
    function a_promoter_can_view_the_10_most_recent_orders_for_their_concert()
    {
        $user = factory(User::class)->create();
        $concert = ConcertFactory::createPublished(['user_id' => $user->id]);

        $oldOrder = OrderFactory::createPublished($concert, ['created_at' => Carbon::parse('11 days ago')]);
        $recentOrder1 = OrderFactory::createPublished($concert, ['created_at' => Carbon::parse('10 days ago')]);
        $recentOrder2 = OrderFactory::createPublished($concert, ['created_at' => Carbon::parse('9 days ago')]);
        $recentOrder3 = OrderFactory::createPublished($concert, ['created_at' => Carbon::parse('8 days ago')]);
        $recentOrder4 = OrderFactory::createPublished($concert, ['created_at' => Carbon::parse('7 days ago')]);
        $recentOrder5 = OrderFactory::createPublished($concert, ['created_at' => Carbon::parse('6 days ago')]);
        $recentOrder6 = OrderFactory::createPublished($concert, ['created_at' => Carbon::parse('5 days ago')]);
        $recentOrder7 = OrderFactory::createPublished($concert, ['created_at' => Carbon::parse('4 days ago')]);
        $recentOrder8 = OrderFactory::createPublished($concert, ['created_at' => Carbon::parse('3 days ago')]);
        $recentOrder9 = OrderFactory::createPublished($concert, ['created_at' => Carbon::parse('2 days ago')]);
        $recentOrder10 = OrderFactory::createPublished($concert, ['created_at' => Carbon::parse('1 days ago')]);

        $response = $this->actingAs($user)->get("/backstage/published-concerts/{$concert->id}/orders");

        $response->data('orders')->assertEquals([
            $recentOrder10,
            $recentOrder9,
            $recentOrder8,
            $recentOrder7,
            $recentOrder6,
            $recentOrder5,
            $recentOrder4,
            $recentOrder3,
            $recentOrder2,
            $recentOrder1,
        ]);

        $response->data('orders')->assertNotContains($oldOrder );
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