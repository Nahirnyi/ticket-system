<?php

use Carbon\Carbon;
use App\User;
/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Concert::class, function (FAker\Generator $faker){
   return [
       'user_id' => function () {
           return factory(User::class)->create()->id;
       },
       'title' => 'The Red Chord',
       'subtitle' => 'with Animosity',
       'date' => Carbon::parse('+2 weeks'),
       'ticket_price' => 2000,
       'ticket_quantity' => 5,
       'venue' => 'The Most Pit',
       'venue_address' => '123 Example Lane',
       'city' => 'Laravel',
       'state' => 'ON',
       'zip' => '17916',
       'additional_information' => 'For tickets, call (555) 555-5555',
   ];
});

$factory->state(App\Concert::class, 'published', function ($faker){
   return [
     'published_at' => Carbon::parse('-1 week'),
   ];
});

$factory->state(App\Concert::class, 'unpublished', function ($faker){
    return [
        'published_at' => null,
    ];
});

$factory->define(App\Ticket::class, function (FAker\Generator $faker){
    return [
        'concert_id' => function () {
            return factory(\App\Concert::class)->create()->id;
        },
    ];
});

$factory->state(App\Ticket::class, 'reserved', function ($faker){
    return [
        'reserved_at' => Carbon::now(),
    ];
});

$factory->define(App\Order::class, function (FAker\Generator $faker){
    return [
        'amount' => 5250,
        'email' => 'somebody@example.com',
        'confirmation_number' => 'ORDERCONFIRMATION123',
        'card_last_four' => '1234',
    ];
});

$factory->define(App\Invitation::class, function (Faker\Generator $faker){
    return [
        'email' => 'somebody@example.com',
        'code' => 'TESTCODE1234',
    ];
});