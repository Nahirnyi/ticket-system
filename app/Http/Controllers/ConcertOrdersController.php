<?php

namespace App\Http\Controllers;

use App\Order;
use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Concert;
use App\Exceptions\NotEnoughTicketsExaption;
use Illuminate\Http\Request;

class ConcertOrdersController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId)
    {
        $concert = Concert::published()->findOrFail($concertId);
        $this->validate(request(),[
           'email' => ['required', 'email'],
            'ticket_quantity' => ['required', 'integer', 'min:1'],
            'payment_token' => ['required'],
        ]);

        try {
            $tickets = $concert->findTickets(request('ticket_quantity'));
            $this->paymentGateway->charge($tickets->sum('price'), request('payment_token'));

            $order = Order::forTickets($tickets, request('email'), $tickets->sum('price'));


            return response()->json($order, 201);

        } catch (PaymentFailedException $e)
        {
            return response()->json([], 422);
        } catch (NotEnoughTicketsExaption $e)
        {
            return response()->json([], 422);
        }

    }
}
