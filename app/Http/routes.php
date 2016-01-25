<?php

/**
 * TODO: Windows authentication in order to user Notifications center.
 */
Route::get('getwptoken', function(){
	// client_id: ms-app://s-1-15-2-3920366865-733337861-2716891985-1299477039-167408987-1424105651-2138086432
	// client_secret: Ucj/v/tIkNseT7+MqE+IL37W+4/aEfU5  
	// 
    // "grant_type=client_credentials&client_id=ms-app://s-1-15-2-3920366865-733337861-2716891985-1299477039-167408987-1424105651-2138086432&client_secret={1}&scope=notify.windows.com", urlEncodedSid, urlEncodedSecret);
    // Headers: ("Content-Type", "application/x-www-form-urlencoded");
	// response = client.UploadString("https://login.live.com/accesstoken.srf", body);

		$client_id = "ms-app://s-1-15-2-3920366865-733337861-2716891985-1299477039-167408987-1424105651-2138086432";

		$client_secret = "Ucj/v/tIkNseT7+MqE+IL37W+4/aEfU5";

		$client = new \GuzzleHttp\Client();

		$host = "https://login.live.com/";
		$body = "grant_type=client_credentials&client_id=" . urlencode($client_id) . "&client_secret=" . urlencode($client_secret) . "&scope=notify.windows.com";
		
		$res = $client->request('POST', $host . $body );
		
		$json = json_decode( $res->getBody() );
		// $json = json_decode($jsoncoded, true);

		DB::table('windows_phone_auth')->insert(
				['token' => $json->access_token]
			);
});

/**
 * Guest routes. No authentication required.
 */
Route::group(['middleware' => 'guest'], function() {
	// 
	Route::get('/', function () {
	    return ['Filmap' => 'awesome'];
	});

	// Authentication
	Route::post('authenticate', 'Auth\AuthenticateController@authenticate');	

	// Save user
	Route::post('user', 'UserController@store');
});

/**
 * Authentication-required routes
 */
Route::group(['middleware' => 'jwt.auth'], function() {

	/**
	 * User-related routes
	 */
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

	/**
	 * Film-related routes
	 */
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

/*
	Get near films based on coordinates

	@param radius, lat, long
	@return json with omdb_id, (lat, long) and radius
*/
Route::get('near/{radius},{lat},{long}', 'GeoController@nearFilms');
