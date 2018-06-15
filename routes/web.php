<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function (){
    return 'Laravel';
});


Route::post('/register', 'Auth\RegisterController@register')->name('auth.register');

Route::get('/concerts/{id}', 'ConcertsController@show')->name('concerts.show');

Route::post('/concerts/{id}/orders', 'ConcertOrdersController@store');

Route::get('/orders/{confirmationNumber}', 'OrderController@show');

Route::post('/login', 'Auth\LoginController@login');

Route::get('/login', 'Auth\LoginController@loginForm');

Route::post('/logout', 'Auth\LoginController@logout');

Route::get('/invitation/{code}', 'InvitationController@show')->name('invitation.show');


Route::group(['middleware' => 'auth', 'prefix' => 'backstage', 'namespace' => 'Backstage'], function (){
    Route::get('concerts/new', 'ConcertsController@create');
    Route::post('concerts', 'ConcertsController@store');
    Route::get('/concerts', 'ConcertsController@index')->name('backstage.concerts.index');
    Route::get('concerts/{id}/edit', 'ConcertsController@edit')->name('backstage.concerts.edit');
    Route::patch('concerts/{id}', 'ConcertsController@update')->name('backstage.concerts.update');
    Route::get('/published-concerts/{concertId}/orders', 'PublishedConcertOrdersController@index')->name('backstage.published-concert-orders.index');
    Route::get('/concerts/{id}/messages/new', 'ConcertMessagesController@create')->name('backstage.concert-messages.new');
    Route::post('/concerts/{id}/messages', 'ConcertMessagesController@store')->name('backstage.concert-messages.store');
});
Route::post('backstage/published-concerts', 'Backstage\PublishedConcertsController@store');


