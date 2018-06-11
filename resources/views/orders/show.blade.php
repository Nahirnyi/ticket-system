<p>{{ $order->confirmation_number }}</p>
<p>${{number_format($order->amount / 100, 2)}}</p>
<p>**** **** **** {{ $order->card_last_four }}</p>
@foreach($order->tickets as $ticket)
    <p>{{ $ticket->code }}</p>
    <p>{{$ticket->concert->date->format('l, F j, Y')}}</p>
    <p>{{ $ticket->concert->date->format('g:ia') }}</p>
@endforeach
