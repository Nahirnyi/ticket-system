<?php

namespace App\Http\Controllers;

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
            $order = $concert->orderTickets(request('email'), request('ticket_quantity'));

            $ticketQuantity = request('ticket_quantity');
            $amount = $ticketQuantity * $concert->ticket_price;
            $token = request('payment_token');
            $this->paymentGateway->charge($amount, $token);


            return response()->json([
                'email' => $order->email,
                'ticket_quantity' => $ticketQuantity,
                'amount' => $amount,
            ], 201);

        } catch (PaymentFailedException $e)
        {
            $order->cancel();
            return response()->json([], 422);
        } catch (NotEnoughTicketsExaption $e)
        {
            return response()->json([], 422);
        }

    }
}
