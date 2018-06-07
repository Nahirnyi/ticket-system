<?php

use App\Billing\PaymentFailedException;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FakePaymentGatewayTest extends TestCase
{

    /** @test */
    function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }

    /** @test */
    function charges_with_an_invalid_payment_token_fail()
    {
        try{
            $paymentGateway = new FakePaymentGateway;

            $paymentGateway->charge(2500, 'invalid-payment-token');
        } catch (PaymentFailedException $e){
            return;
        }

        $this->fail();

    }

    /** @test */
    function running_a_hook_before_the_first_charge()
    {
        $paymentGateway = new FakePaymentGateway;
        $callbackRun = false;

        $paymentGateway->beforeFirstCharge(function ($paymentGateway) use (&$callbackRun){
            $callbackRun = true;
           $this->assertEquals(0, $paymentGateway->totalCharges());
        });

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        $this->assertTrue($callbackRun);
        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }
}

