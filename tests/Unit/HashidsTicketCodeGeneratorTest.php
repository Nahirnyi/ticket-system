<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/11/18
 * Time: 5:26 PM
 */
namespace Tests\Unit;

use Tests\TestCase;
class HashidsTicketCodeGeneratorTest extends TestCase
{
    /** @test */
    function ticket_codes_are_at_least_6_characters_long()
    {
        $ticketCodeGenerator = new \App\HashidsTicketCodeGenerator('testsalt1');

        $code = $ticketCodeGenerator->generateFor(new \App\Ticket(['id' => 1]));

        $this->assertTrue(strlen($code) >= 6);
    }

    /** @test */
    function ticket_codes_can_only_contain_uppercase_letters()
    {
        $ticketCodeGenerator = new \App\HashidsTicketCodeGenerator('testsalt1');

        $code = $ticketCodeGenerator->generateFor(new \App\Ticket(['id' => 1]));

        $this->assertRegExp('/^[A-Z]+$/', $code);
    }

    /** @test */
    function ticket_codes_for_the_same_ticket_id_are_the_same()
    {
        $ticketCodeGenerator = new \App\HashidsTicketCodeGenerator('testsalt1');

        $code1 = $ticketCodeGenerator->generateFor(new \App\Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator->generateFor(new \App\Ticket(['id' => 1]));

        $this->assertEquals($code1, $code2);
    }

    /** @test */
    function ticket_codes_for_different_ticket_ids_are_different()
    {
        $ticketCodeGenerator = new \App\HashidsTicketCodeGenerator('testsalt1');

        $code1 = $ticketCodeGenerator->generateFor(new \App\Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator->generateFor(new \App\Ticket(['id' => 2]));

        $this->assertNotEquals($code1, $code2);
    }

    /** @test */
    function ticket_codes_with_different_salts_are_different()
    {
        $ticketCodeGenerator1 = new \App\HashidsTicketCodeGenerator('testsalt1');
        $ticketCodeGenerator2 = new \App\HashidsTicketCodeGenerator('testsalt2');

        $code1 = $ticketCodeGenerator1->generateFor(new \App\Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator2->generateFor(new \App\Ticket(['id' => 1]));

        $this->assertNotEquals($code1, $code2);
    }
}