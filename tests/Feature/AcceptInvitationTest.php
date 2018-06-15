<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Hash;
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

        $response->assertStatus(404);
    }

    /** @test */
    public function registering_with_a_valid_invitation_code()
    {
        $invitation = factory(Invitation::class)->create([
            'code' => 'TESTCODE123',
            'user_id' => null,
        ]);
        $response = $this->post('/register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE123'
        ]);

        $response->assertRedirect('/backstage/concerts');

        $this->assertEquals(1, User::count());
        $user = User::first();
        $this->assertAuthenticatedAs($user);

        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue(Hash::check('secret', $user->password));
        $this->assertTrue($invitation->fresh()->user->is($user));
    }

    /** @test */
    public function registering_with_a_used_invitation_code()
    {
        $invitation = factory(Invitation::class)->create([
            'code' => 'TESTCODE123',
            'user_id' => factory(User::class)->create(),
        ]);

        $this->assertEquals(1, User::count());


        $response = $this->post('/register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE123'
        ]);

        $response->assertStatus(404);
        $this->assertEquals(1, User::count());

    }

    /** @test */
    public function registering_with_a_invitation_code_that_does_not_exist()
    {
        $response = $this->post('/register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE123'
        ]);

        $response->assertStatus(404);
        $this->assertEquals(0, User::count());

    }
}
