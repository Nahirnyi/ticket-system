<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/11/18
 * Time: 11:24 AM
 */

namespace App;


use Hashids\Hashids;

class HashidsTicketCodeGenerator implements TicketCodeGenerator
{
    private $hashids;

    public function __construct($salt)
    {
        $this->hashids = new Hashids($salt, 6, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }

    public function generateFor($ticket)
    {
        return $this->hashids->encode($ticket->id);
    }
}