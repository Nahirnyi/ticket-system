<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/11/18
 * Time: 12:24 PM
 */

namespace App\Billing;


class Charge
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function cardLastFour()
    {
        return $this->data['card_last_four'];
    }

    public function amount()
    {
        return $this->data['amount'];
    }
}