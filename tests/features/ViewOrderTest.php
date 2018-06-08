<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/8/18
 * Time: 4:04 PM
 */

use App\Order;
use App\Concert;
use App\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ViewOrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function user_can_view_their_order_confirmation()
    {
        $concert = factory(Concert::class)->create();
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234'
        ]);
        $ticket = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id
        ]);

        $response = $this->get("/orders/ORDERCONFIRMATION1234");

        $response->assertStatus(200);

        $response->assertViewHas('order', function ($viewOrder) use ($order){
            return $order->id === $viewOrder->id;
        });
    }
}