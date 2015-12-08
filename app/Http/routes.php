<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => 'guest'], function() {
	Route::get('/', function () {
	    return view('welcome');
	});

	Route::post('authenticate', 'Auth\AuthenticateController@authenticate');	

	// Save user
	Route::post('user', 'UserController@store');
});

/*
	Get near films based on coordinates

	@param radius, lat, long
	@return json with omdb_id, (lat, long) and radius
*/
Route::get('near/{radius},{lat},{long}', 'GeoController@nearFilms');

Route::group(['middleware' => 'jwt.auth'], function() {

	Route::group(['prefix' => 'user'], function() {
		// Get all users
		Route::get('/', 'UserController@index');

		// Get specific user
		Route::get('{id}', 'UserController@show');
		
		// Update user
		Route::put('{id}', 'UserController@update');

		// Delete user
		Route::delete('{id}', 'UserController@destroy');

	});

	Route::group(['prefix' => 'films'], function() {
		// Get all films
		Route::get('/', 'FilmController@index');

		// Save film
		Route::post('/', 'FilmController@store');

		// Get specific user's film
		Route::get('{omdb}', 'FilmController@show');

		// Delete film for user 
		Route::delete('{omdb}', 'FilmController@destroy');

		// Update film as watched
		Route::post('{omdb}/watch', 'FilmController@watch');
	});
	
});
