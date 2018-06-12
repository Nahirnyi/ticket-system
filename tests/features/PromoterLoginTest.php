<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/12/18
 * Time: 11:33 AM
 */

use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PromoterLoginTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function logging_in_with_valid_credentials()
    {
        $user = factory(User::class)->create([
            'email' => 'jne@example.com',
            'password' => bcrypt('super-secret-password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'jne@example.com',
            'password' => 'super-secret-password',
        ]);

        $response->assertRedirect('/backstage/concerts');
        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->is($user));
    }

    /** @test */
    function logging_in_with_invalid_credentials()
    {
        $user = factory(User::class)->create([
            'email' => 'jne@example.com',
            'password' => bcrypt('super-secret-password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'jne@example.com',
            'password' => 'not-the-right-password',
        ]);

        $response->assertRedirect('/login');
        $this->assertFalse(Auth::check());
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    function logging_in_with_an_account_that_does_not_exist()
    {
        $response = $this->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'not-the-right-password',
        ]);

        $response->assertRedirect('/login');
        $this->assertFalse(Auth::check());
        $response->assertSessionHasErrors('email');
    }
}