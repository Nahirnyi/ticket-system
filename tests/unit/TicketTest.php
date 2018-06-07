<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/7/18
 * Time: 10:51 AM
 */

use App\Ticket;

class TicketTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;

    /** @test */
    function a_ticket_can_be_reserved()
    {
        $ticket = factory(Ticket::class)->create();
        $this->assertNull($ticket->reserved_at);

        $ticket->reserved();

        $this->assertNotNull($ticket->fresh()->reserved_at);
    }

    /** @test */
    function a_ticket_can_be_released()
    {
        $concert = factory(\App\Concert::class)->create();
        $concert->addTickets(1);
        $order = $concert->orderTickets('jane@example.com', 1);
        $ticket = $order->tickets()->first();
        $this->assertEquals($order->id, $ticket->order_id);

        $ticket->release();

        $this->assertNull($ticket->fresh()->order_id);
    }
}