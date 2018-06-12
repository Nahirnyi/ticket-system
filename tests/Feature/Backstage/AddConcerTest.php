<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/12/18
 * Time: 4:32 PM
 */

namespace Tests\Feature\Backstage;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddConcerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function promoters_can_view_the_add_concert_form()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('backstage/concerts/new');

        $response->assertStatus(200);
    }

    /** @test */
    function guests_cannot_view_the_add_concert_form()
    {
        $response = $this->get('backstage/concerts/new');

        $response->assertStatus(302);
        $response->assertRedirect('/login');

    }
}