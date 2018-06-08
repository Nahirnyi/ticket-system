<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/8/18
 * Time: 9:47 AM
 */

use App\Billing\StripePaymentGateway;
use App\Billing\PaymentFailedException;

/**
 * Class StripePaymentGatewayTest
 * @group integration
 */
class StripePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTests;

    protected function getPaymentGateway()
    {
        return new StripePaymentGateway(config('services.stripe.secret'));
    }
}