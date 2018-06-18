<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/11/18
 * Time: 11:24 AM
 */

namespace App;


class RandomOrderConfirmationNumberGenerator implements OrderConfirmationNumberGenerator, InvitationCodeGenerator
{
    public function generate()
    {
        $pool = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        return substr(str_shuffle(str_repeat($pool, 24)), 0, 24);
    }
}