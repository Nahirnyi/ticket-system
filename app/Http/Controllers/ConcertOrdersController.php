<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Concert;
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
        $this->validate(request(),[
           'email' => ['required', 'email'],
            'ticket_quantity' => ['required', 'integer', 'min:1'],
            'payment_token' => ['required'],
        ]);
        try{
            $concert = Concert::find($concertId);
            $ticketQuantity = request('ticket_quantity');
            $amount = $ticketQuantity * $concert->ticket_price;
            $token = request('payment_token');
            $this->paymentGateway->charge($amount, $token);

            $order = $concert->orderTickets(request('email'), request('ticket_quantity'));

            return response()->json([], 201);
        } catch (PaymentFailedException $e)
        {
            return response()->json([], 422);
        }

    }
}
