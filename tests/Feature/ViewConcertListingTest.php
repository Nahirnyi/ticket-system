<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Concert;
use Carbon\Carbon;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewConcertListingTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function user_can_view_a_published_concert_listing()
    {
        $concert = factory(Concert::class)->states('published')->create([
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity',
            'date' => Carbon::parse('December 13, 2016 8:00pm'),
            'ticket_price' => 3250,
            'venue' => 'The Most Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laravel',
            'state' => 'ON',
            'zip' => '17916',
            'additional_information' => 'For tickets, call (555) 555-5555',
            'published_at' => Carbon::parse('-1 week'),
        ]);

        $response = $this->get('/concerts/' . $concert->id);

        $response->assertStatus(200);
        $response->assertSee('The Red Chord');
        $response->assertSee('with Animosity');
        $response->assertSee('December 13, 2016');
        $response->assertSee('8:00pm');
        $response->assertSee('32.50');
        $response->assertSee('123 Example Lane');
        $response->assertSee('Laravel, ON 17916');
        $response->assertSee('The Red Chord');
        $response->assertSee('For tickets, call (555) 555-5555');
    }

    /** @test */
    function user_cannot_view_unpublished_concert_listing()
    {
        $concert = factory(Concert::class)->states('unpublished')->create();

        $response = $this->get('/concerts/' . $concert->id);

        $response->assertStatus(404);
    }
}

