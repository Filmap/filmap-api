<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Film;
use App\Geo;

use App\Events\FilmWasStored;
use Event;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class FilmController extends Controller
{
    /**
     * Returns a listing of auth user's films.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $films = $user->films->toJson();

        return $films;
    }

    /**
     * Auxiliar function to manage the notification event.
     *
     * Everytime a film is stored, it's checked whether there
     * are any other films by a 1km-radius. 
     *
     * If any films are found, an Event is triggered and a 
     * notification is sent to those users.
     * 
     * @param  $geo coordinates 
     * @param  $user user who saved the film 
     * @param  $film film being stored 
    */
    public function sendNotification($geo, $user, $film)
    {
        // Get users' id that marked a film nearby
        // 1km radius
        $users = Film::near(1, $geo->lat, $geo->lng)
                    ->get()
                    ->unique("user_id")
                    ->pluck("user_id")
                    ->all();

        // Remove authenticated user from the search
        if(($key = array_search($user->id, $users)) !== false) {
            unset($users[$key]);
        }

        // If there were any films nearby, send the notific
        if ( ! empty($users) ) {
            Event::fire(new FilmWasStored($users, $user->id, $film));
        }
    }

    /**
     * Store a newly created film in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'omdb' => 'required|string',
            'watched' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(["response" => False, "errors" => $validator->getMessageBag()->toArray()], 400);
        }

        $user = JWTAuth::parseToken()->authenticate();

        // Avoid duplicate films
        if ($user->films->contains('omdb', $request['omdb'])) {
            return response()->json(["response" => False, "errors" => "Film already marked"], 400);
        }

        $film = new Film([
                'omdb' => $request['omdb'],
                'watched' => $request['watched'],
        ]);

        $user->films()->save($film);

        // If location info was given
        if ($request->has('lat') && $request->has('lng')) {

            $geo = new Geo ([
                'lat' => $request['lat'],
                'lng' => $request['lng'],
            ]);

            // Send notification
            $this->sendNotification($geo, $user, $film);

            $film->geo()->save($geo);
        }

        return response()->json(["response" => True], 200);
    }

    /**
     * Display the specified film.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $film = Film::findOrFail($id);
        $film->response = True;

        return json_encode($film);
    }

    /**
     * Mark film as watched.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function watch($id)
    {
        $film = Film::findOrFail($id);

        $user = JWTAuth::parseToken()->authenticate();
        
        if ($user != $film->user) {
            return response()->json(["response" => False, "error" => "Permission denied"], 403);
        }

        $film->watched = True;
 
        $film->save();
    }

    /**
     * Remove the specified film from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $film = Film::findOrFail($id);

        $user = JWTAuth::parseToken()->authenticate();
        
        if ($user != $film->user) {
            return response()->json(["response" => False, "error" => "Permission denied"], 403);
        }

        $film->delete();
    }
}
