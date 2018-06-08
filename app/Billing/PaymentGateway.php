<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/6/18
 * Time: 3:51 PM
 */

namespace App\Billing;


interface PaymentGateway
{
    public function charge($amount, $token);

    public function getValidTestToken();

    public function newChargesDuring($callback);
}