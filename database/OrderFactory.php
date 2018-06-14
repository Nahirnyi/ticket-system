<?php

use App\Concert;
use App\Ticket;
use App\Order;

class OrderFactory
{
    public static function createPublished($concert, $overrides = [], $ticketsQuantity = 1)
    {
        $order = factory(Order::class)->create($overrides);
        $tickets = factory(Ticket::class, $ticketsQuantity)->create(['concert_id' => $concert->id]);
        $order->tickets()->saveMany($tickets);
        return $order;
    }
}

