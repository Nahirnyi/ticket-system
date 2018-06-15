<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AcceptInvitationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function viewing_an_unused_invitation()
    {
        $invitation = factory(Invitation::class)->create([
            'code' => 'TESTCODE123'
        ]);

        $response = $this->get('/invitation/TESTCODE123');

        $response->assertStatus(200);
        $response->assertViewIs('invitation.show');
        $this->assertTrue($response->data('invitation')->is($invitation));
    }

    /** @test */
    public function viewing_a_used_invitation()
    {
        $invitation = factory(Invitation::class)->create([
            'code' => 'TESTCODE123',
            'user_id' => null,
        ]);

        $response = $this->get('/invitation/TESTCODE123');

        $response->assertStatus(404 );
        $response->assertViewIs('invitation.show');
        $this->assertTrue($response->data('invitation')->is($invitation));
    }

    /** @test */
    public function viewing_an_invitation_that_does_not_exist()
    {
        $response = $this->get('/invitation/TESTCODE123');

        $response->assertStatus(404 );
    }
}
