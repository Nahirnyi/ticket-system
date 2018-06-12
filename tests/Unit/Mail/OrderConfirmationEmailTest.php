<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/12/18
 * Time: 11:02 AM
 */
namespace Tests\Unit\Mail;

use Tests\TestCase;
use App\Order;
use App\Mail\OrderConfirmationEmail;

class OrderConfirmationEmailTest extends TestCase
{
    /** @test */
    function email_contains_a_link_to_the_order_confirmation_test()
    {
        $order = factory(Order::class)->make([
            'confirmation_number' => 'ORDERCONFIRMATION1234'
        ]);

        $email = new OrderConfirmationEmail($order);
        // IN Laravel 5.5
        //$rendered = $email->render()

        $rendered = $this->render($email);
        $this->assertContains(url('/orders/ORDERCONFIRMATION1234'), $rendered);
    }

    private function render($mailable)
    {
        $mailable->build();
        return view($mailable->view, $mailable->buildViewData())->render();
    }

    /** @test */
    function email_has_a_subject()
    {
        $order = factory(Order::class)->make();
        $email = new OrderConfirmationEmail($order);
        $this->assertEquals('Your TicketBeast Order', $email->build()->subject);
    }
}