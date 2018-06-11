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
    function creating_an_order_from_tickets_email_and_amount()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(5);
        $this->assertEquals(5, $concert->ticketsRemaining());

        $order = Order::forTickets($concert->findTickets(3), 'john@example.com', 3600);

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(2, $concert->ticketsRemaining());

    }

    /** @test */
    function retrieving_an_order_by_confirmation_number()
    {
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
        ]);

        $foundOrder = Order::findByConfirmationNumber('ORDERCONFIRMATION1234');
        $this->assertEquals($order->id, $foundOrder->id);
    }

    /** @test */
    function retrieving_a_nonexistent_order_by_confirmation_number_throws_an_exception()
    {
        try {
            Order::findByConfirmationNumber('ORDERCONFIRMATION1234');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return;
        }

        $this->fail('No matching order was found');
    }

    /** @test */
    function converting_to_an_array()
    {
        /*$concert = factory(Concert::class)->create(['ticket_price'=> 1200]);
        $concert->addTickets(5);
        $order = $concert->orderTickets('jane@example.com', 5);*/

        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'email' => 'jane@example.com',
            'amount' => 6000,
        ]);
        $order->tickets()->saveMany(factory(\App\Ticket::class)->times(5)->create());

        $result = $order->toArray();

        $this->assertEquals([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'email' => 'jane@example.com',
            'amount' => 6000,
            'ticket_quantity' => 5,
        ], $result);
    }

}