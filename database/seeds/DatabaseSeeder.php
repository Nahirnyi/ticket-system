<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Concert::class)->states('published')->create([
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity',
            'date' => Carbon::parse('+2 weeks'),
            'ticket_price' => 2000,
            'venue' => 'The Most Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laravel',
            'state' => 'ON',
            'zip' => '17916',
            'additional_information' => 'For tickets, call (555) 555-5555',
        ])->addTickets(10);
    }
}
