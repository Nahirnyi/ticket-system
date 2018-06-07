<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/7/18
 * Time: 10:11 AM
 */

use App\Order;
use App\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class OrderTest extends  TestCase
{
    use DatabaseMigrations;

    /** @test */
    function converting_to_an_array()
    {
        $concert = factory(Concert::class)->create(['ticket_price'=> 1200]);
        $concert->addTickets(5);
        $order = $concert->orderTickets('jane@example.com', 5);

        $result = $order->toArray();

        $this->assertEquals([
            'email' => 'jane@example.com',
            'amount' => 6000,
            'ticket_quantity' => 5,
        ], $result);
    }

    /** @test */
    function tickets_are_released_when_an_order_is_cancelled()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(10);
        $order = $concert->orderTickets('jane@example.com', 5);
        $this->assertEquals(5, $concert->ticketsRemaining());

        $order->cancel();

        $this->assertEquals(10, $concert->ticketsRemaining());
        $this->assertNull(Order::find($order->id));
    }

}