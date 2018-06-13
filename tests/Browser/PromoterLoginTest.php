<?php

namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PromoterLoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function logging_in_with_invalid_credetials()
    {
        $user = factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => 'super-secret-password'
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->press('Log')
                ->assertPathIs('/login');
        });
    }
}
