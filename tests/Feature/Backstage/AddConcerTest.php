<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/12/18
 * Time: 4:32 PM
 */

namespace Tests\Feature\Backstage;

use Carbon\Carbon;
use App\User;
use App\Concert;
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

    /** @test */
    function guests_cannot_add_new_concerts()
    {
        $user = factory(User::class)->create();

        $response = $this->post('/backstage/concerts', [
            'title' => 'No Warning',
            'subtitle' => 'with Cruel Hand and Backtrack',
            'additional_information' => 'Your musr be 19 years',
            'date' => '2017-11-18',
            'time' => '8:00pm',
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Fake St.',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '12345',
            'ticket_price' => '32.50',
            'ticket_quantity' => '75',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertEquals(0, Concert::count());
    }
}