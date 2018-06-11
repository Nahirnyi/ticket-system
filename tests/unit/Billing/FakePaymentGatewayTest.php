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

