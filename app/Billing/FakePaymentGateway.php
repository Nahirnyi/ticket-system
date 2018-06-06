<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/6/18
 * Time: 3:51 PM
 */

namespace App\Billing;


class FakePaymentGateway implements PaymentGateway
{
    private $charges;

    public function __construct()
    {
        $this->charges = collect();
    }

    public function getValidTestToken(){
        return "valid-token";
    }

    public function charge($amount, $token)
    {
        $this->charges[] = $amount;
    }

    public function totalCharges()
    {
        return $this->charges->sum();
    }
}