<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/13/18
 * Time: 4:26 PM
 */

namespace Tests\Feature\Backstage;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Concert;
use App\User;
use Tests\TestCase;

class ViewConcertListTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function guests_cannot_view_a_promoters_concert_list()
    {
        $response = $this->get('/backstage/concerts');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    function promoter_can_view_a_list_of_their_concerts()
    {
        $user = factory(User::class)->create();
        $concerts = factory(Concert::class, 3)->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get('/backstage/concerts');

        $response->assertStatus(200);

        $this->assertTrue($response->original->getData()['concerts']->contains($concerts[0]));
    }
}