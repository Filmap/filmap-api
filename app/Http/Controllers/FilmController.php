<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Film;
use App\Geo;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class FilmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $films = $user->films->push(["Response" => true])->toJson();

        return $films;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'omdb' => 'required|integer',
            'watched' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(["response" => False, "errors" => $validator->getMessageBag()->toArray()], 400);
        }

        $user = JWTAuth::parseToken()->authenticate();

        if ($user->films->contains('omdb', $request['omdb'])) {
            return response()->json(["response" => False, "errors" => "Film already marked"], 400);
        }

        $film = new Film([
                'omdb' => $request['omdb'],
                'watched' => $request['watched'],
        ]);

        $user->films()->save($film);

        // Saving the the location

        if ($request->has('lat') && $request->has('lng')) {

            $geo = new Geo ([
                'lat' => $request['lat'],
                'lng' => $request['lng'],
            ]);

            $film->geo()->save($geo);
        }

        // return redirect()->route('result')->with(['confirmed' => True]);
    }

    /**
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
