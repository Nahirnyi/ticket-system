<?php
use App\Concert;
use Carbon\Carbon;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewConcertListing extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function user_can_view_concert_listing()
    {
        $concert = Concert::create([
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
        ]);

        $this->visit('/concerts' . $concert->id);

        $this->see('The Red Chord');
        $this->see('with Animosity');
        $this->see('December 13, 2016');
        $this->see('8:00pm');
        $this->see('32.50');
        $this->see('123 Example Lane');
        $this->see('Laravel ON 17916');
        $this->see('The Red Chord');
        $this->see('For tickets, call (555) 555-5555');
    }
}

