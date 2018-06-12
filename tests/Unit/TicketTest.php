<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/7/18
 * Time: 10:51 AM
 */
namespace Tests\Unit;

use Tests\TestCase;
use App\Order;
use App\Ticket;
use App\Facades\TicketCode;

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
        $ticket = factory(\App\Ticket::class)->states('reserved')->create();
        $this->assertNotNull($ticket->reserved_at);

        $ticket->release();

        $this->assertNull($ticket->fresh()->reserved_at);
    }

    /** @test */
    function a_ticket_can_be_claimed_for_an_order()
    {
        $order = factory(Order::class)->create();
        $ticket = factory(Ticket::class)->create(['code' => null]);
        TicketCode::shouldReceive('generateFor')->with($ticket)->andReturn('TICKETCODE1');
        $this->assertNull($ticket->code);
        $ticket->claimFor($order);

        $this->assertContains($ticket->id, $order->tickets->pluck('id'));
        $this->assertEquals('TICKETCODE1', $ticket->code);
    }
}