<?php

namespace App\Http\Controllers;

use App\Events\FilmWasStored;
use App\Film;
use App\Geo;
use App\User;
use Event;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;

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
                    ->unique('user_id')
                    ->pluck('user_id')
                    ->all();

        // Remove authenticated user from the search
        if (($key = array_search($user->id, $users)) !== false) {
            unset($users[$key]);
        }

        // If there were any films nearby, send the notific
        if (!empty($users)) {
            Event::fire(new FilmWasStored($users, $user->id, $film));
        }
    }

    /**
     * Store a newly created film in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'omdb'    => 'required|string',
            'watched' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['response' => false, 'errors' => $validator->getMessageBag()->toArray()], 400);
        }

        $user = JWTAuth::parseToken()->authenticate();

        // Avoid duplicate films
        if ($user->films->contains('omdb', $request['omdb'])) {
            return response()->json(['response' => false, 'errors' => 'Film already marked'], 400);
        }

        $film = new Film([
                'omdb'    => $request['omdb'],
                'watched' => $request['watched'],
        ]);

        $user->films()->save($film);

        // If location info was given
        if ($request->has('lat') && $request->has('lng')) {
            $geo = new Geo([
                'lat' => $request['lat'],
                'lng' => $request['lng'],
            ]);

            // Send notification
            $this->sendNotification($geo, $user, $film);

            $film->geo()->save($geo);
        }

        return response()->json(['response' => true], 200);
    }

    /**
     * Display the specified film.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($omdb)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $film = $user->films->where('omdb', $omdb)->first();

        if (is_null($film)) {
            return response()->json(['response' => false, 'errors' => "User doesn't have this film"], 404);
        }

        return json_encode($film);
    }

    /**
     * Mark film as watched.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function watch($omdb)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $film = $user->films->where('omdb', $omdb)->first();

        if (is_null($film)) {
            return response()->json(['response' => false, 'errors' => "User doesn't have this film"], 404);
        }

        if ($user->id != $film->user->id) {
            return response()->json(['response' => false, 'error' => 'Permission denied'], 403);
        }

        $film->watched = true;
        $film->save();
    }

    /**
     * Remove the specified film from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($omdb)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $film = $user->films->where('omdb', $omdb)->first();

        if (is_null($film)) {
            return response()->json(['response' => false, 'errors' => "User doesn't have this film"], 404);
        }

        if ($user->id != $film->user->id) {
            return response()->json(['response' => false, 'error' => 'Permission denied'], 403);
        }

        $film->delete();
        return response()->json(['response' => true], 200);
    }
}
