<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/11/18
 * Time: 12:05 PM
 */

namespace App\Facades;


use App\InvitationCodeGenerator;
use Illuminate\Support\Facades\Facade;

class InvitationCode extends Facade
{
    protected static function getFacadeAccessor()
    {
       return InvitationCodeGenerator::class;
    }
}