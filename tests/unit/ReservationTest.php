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
}