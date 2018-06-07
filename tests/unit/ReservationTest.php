<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/7/18
 * Time: 1:58 PM
 */

use App\Reservation;
use App\Concert;

class ReservationTest extends TestCase
{
    /** @test */
    function calculating_the_total_cost()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets);

        $this->assertEquals(3600, $reservation->totalCost());
    }

    /** @test */
    function reserved_tickets_are_relased_when_a_reservation_is_cancelled()
    {
        $ticket1 = Mockery::mock(\App\Ticket::class);
        $ticket1->shouldReceive('release')->once();

        $ticket2 = Mockery::mock(\App\Ticket::class);
        $ticket2->shouldReceive('release')->once();

        $ticket3 = Mockery::mock(\App\Ticket::class);
        $ticket3->shouldReceive('release')->once();

        $tickets = collect([$ticket1, $ticket2, $ticket3]);

        $reservation = new Reservation($tickets);

        $reservation->cancel();

    }
}