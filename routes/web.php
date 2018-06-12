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

Route::get('/concerts/{id}', 'ConcertsController@show')->name('concerts.show');

Route::post('/concerts/{id}/orders', 'ConcertOrdersController@store');

Route::get('/orders/{confirmationNumber}', 'OrderController@show');

Route::post('/login', 'Auth\LoginController@login');

Route::get('/login', 'Auth\LoginController@loginForm');

Route::post('/logout', 'Auth\LoginController@logout');


Route::group(['middleware' => 'auth', 'prefix' => 'backstage'], function (){
    Route::get('concerts/new', 'Backstage\ConcertsController@create');
    Route::post('concerts', 'Backstage\ConcertsController@store');

});
