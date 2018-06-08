<?php

use App\Billing\PaymentFailedException;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FakePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTests;

    protected function getPaymentGateway()
    {
        return new FakePaymentGateway;
    }

    /** @test */
    function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = $this->getPaymentGateway();

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        });


        $this->assertCount(1, $newCharges);
        $this->assertEquals(2500, $newCharges->sum());
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
        $callbackRun = 0;

        $paymentGateway->beforeFirstCharge(function ($paymentGateway) use (&$callbackRun){
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
            $callbackRun++;
            $this->assertEquals(2500, $paymentGateway->totalCharges());
        });

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        $this->assertEquals(1, $callbackRun);
        $this->assertEquals(5000, $paymentGateway->totalCharges());
    }
}

